<?php
namespace App\Jobs;
use App\Models\Billdata;
use App\Models\FreightOcrResult;
use App\Services\FreightOcrValidator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class ProcessFreightOcr implements ShouldQueue 
{
	 use Dispatchable,InteractsWithQueue,Queueable,SerializesModels;
	 public int $tries=3;
	 public int $timeout=180;
	 public int $backoff=15;
	
	public function __construct(public int $resultId)
	 {
		 $this->onQueue('freight-ocr');
	}
	 public function handle(FreightOcrValidator $validator): void 
	 {
		  $result=FreightOcrResult::findOrFail($this->resultId);
		  if(in_array($result->status,['valid','invalid','review_required'],true))return;
		  $result->update(['status'=>'processing']);
		  $bill=Billdata::findOrFail($result->billdata_id);
		  
			if($bill->submit || $bill->f_return)
			{
				$result->update(['status'=>'review_required','remarks'=>'Already submitted or returned','reasons'=>['Already submitted or returned']]);return;
			}
			$path=$this->resolveFile($bill->freight_invoice_file);

			if(!$path)
			{
				$result->update(['status'=>'review_required','remarks'=>'Invoice file missing or inaccessible','reasons'=>['Invoice file missing or inaccessible']]);return;
			}
		  $url=rtrim(config('services.freight_ocr.url'),'/').'/api/freight/extract';
		  
		  $key=config('services.freight_ocr.api_key');
		  
		if(!$key || !$url)
		{
			throw new \RuntimeException('Freight OCR service not configured');
		}
	  $invoice=fopen($path,'rb');
	  try {
		   $request=Http::withHeaders(['X-API-Key'=>$key])->timeout(150)->connectTimeout(10)->attach('invoice',$invoice,basename($path));
		   // Combined invoice/LR PDFs are supported. Separate LR files require explicit mapping; do not treat POD as LR.
		   $response=$request->post($url);
		} finally {fclose($invoice);}
			if(!$response->successful())
			throw new \RuntimeException('OCR HTTP '.$response->status());

			$payload=$response->json();
			if(!is_array($payload))
			throw new \RuntimeException('Invalid OCR response');
			[$status,$reasons]=$validator->check($bill,$payload);
			$result->update(['status'=>$status,'reasons'=>$reasons,'remarks'=>implode('; ',$reasons),'extracted'=>$payload]);
	 }
	 private function resolveFile($name): ?string 
	 {
		if(!$name)return null;
		$relative=ltrim(str_replace('\\','/',$name),'/');
		if(str_contains($relative,'..') || !str_starts_with($relative,'uploads/invoice/'))return null;
		$path=public_path($relative);
		return is_file($path)&&is_readable($path)?$path:null;
	 }
	 public function failed(\Throwable $e):void
	 {
	  Log::error('Freight OCR failed',['result_id'=>$this->resultId,'error'=>$e->getMessage()]);
	  FreightOcrResult::whereKey($this->resultId)->update(['status'=>'failed','remarks'=>'OCR service failed; retry required','reasons'=>['OCR service failed; retry required']]);
	 }
}
