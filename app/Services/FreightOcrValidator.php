<?php
namespace App\Services;
class FreightOcrValidator {
 private function value($v): string {return trim((string)($v??''));}
 private function number($v): string {$v=$this->value($v);return ctype_digit($v)?(ltrim($v,'0')?:'0'):strtoupper($v);}
 private function amount($v): ?int {$v=preg_replace('/[^0-9.\-]/','',(string)$v);return is_numeric($v)?(int)round(((float)$v)*100):null;}
 private function date($v): ?string {try {return $v?\Carbon\Carbon::parse($v)->format('Y-m-d'):null;}catch(\Throwable $e){return null;}}
 public function check($bill,array $data): array {
  $errors=[];$review=[];$invoice=$data['invoice']??[];$lr=$data['lr_number_match']??[];
  if (empty($data['success']))$review[]='Invoice text could not be read';
  $checks=[['Invoice number',$bill->freight_invoice_no,$invoice['invoice_number']??null,'text'],['LR number',$bill->lr_no,$invoice['lr_number']??null,'lr'],['Freight PO',$bill->ref2,$invoice['po_number']??null,'text'],['Invoice date',$bill->freight_invoice_date,$invoice['invoice_date']??null,'date']];
  foreach($checks as [$name,$expected,$actual,$kind]){
   if($this->value($expected)==='')continue;
   if($this->value($actual)===''){$review[]="$name not extracted";continue;}
   $a=$kind==='date'?$this->date($expected):($kind==='lr'?$this->number($expected):strtoupper($this->value($expected)));
   $b=$kind==='date'?$this->date($actual):($kind==='lr'?$this->number($actual):strtoupper($this->value($actual)));
   if($a===null||$b===null){$review[]="$name date could not be parsed";continue;}
   if($a!==$b)$errors[]="$name mismatch: expected {$this->value($expected)}, extracted {$this->value($actual)}";
  }
  $expectedAmount=$this->amount($bill->freight_amount);$actualAmount=$this->amount($invoice['amount']??null);
  if($expectedAmount===null)$review[]='Entered freight amount is missing';elseif($actualAmount===null)$review[]='Invoice amount not extracted';elseif($expectedAmount!==$actualAmount)$errors[]='Amount mismatch: entered '.number_format($expectedAmount/100,2).' vs extracted '.number_format($actualAmount/100,2);
  if($this->amount($bill->a_amount)!==null && $expectedAmount!==null && $this->amount($bill->a_amount)!==$expectedAmount)$errors[]='Freight amount does not match bill amount';
  if(empty($lr['match'])){if(!empty($lr['invoice_lr_number']) && !empty($lr['page_2_serial_number']))$errors[]='Invoice LR number differs from scanned LR serial';else $review[]='Scanned LR serial number not independently verified';}
  if($bill->freight_type==='ADHOC' && !$bill->approval_file)$errors[]='ADHOC approval file missing';
  if(!$bill->pod_file)$review[]='POD file missing';
  if(($data['invoice_digital_signature']['cryptographic_status']??'not_verified')!=='verified')$review[]='Invoice digital signature not cryptographically verified';
  $status=$errors?'invalid':($review?'review_required':'valid');
  $reasons=array_merge($errors,$review);
  return [$status,$reasons?:['All mandatory automated checks passed']];
 }
}
