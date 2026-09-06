<div class="card-body p-0">

	@if($user_role == 1)
		<form method="POST"  action="{{ route('admin.billdata.bulkDelete') }}"
			  id="bulkDeleteForm">
			@csrf
	@endif


	<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">

		<table id="billDataAjaxTable" class="table table-bordered border-dark table-hover">

			<thead>
				<tr>
					<th style="background:#fce4d6;color:#0070c0;z-index:999;"
						class="{{ count($billdatalist) > 0 ? 'sticky-col-1' : '' }}">
						<a href="javascript:void(0);" class="bill-sort" 						   data-column="s5_consignor_short_name_and_location"						   style="color:#0070c0;text-decoration:none;">S5 consignor short<br>
							name & location<span class="sort-icon">
								@if($sortBy == 's5_consignor_short_name_and_location')
									{{ $sortDirection == 'asc' ? '▲' : '▼' }}
								@else
									⇅
								@endif
							</span>
						</a>
					</th>
					<th style="background:#fce4d6;color:#0070c0;z-index:999;"						class="{{ count($billdatalist) > 0 ? 'sticky-col-2' : '' }}">

						<a href="javascript:void(0);" class="bill-sort" data-column="d5_consignor_short_name_and_location" style="color:#0070c0;text-decoration:none;">D5 consignor short<br>
							name & location<span class="sort-icon">
								@if($sortBy == 'd5_consignor_short_name_and_location')
									{{ $sortDirection == 'asc' ? '▲' : '▼' }}
								@else
									⇅
								@endif
							</span>
						</a>
					</th>

					<th style="background:#fce4d6;color:#0070c0;z-index:999;" class="{{ count($billdatalist) > 0 ? 'sticky-col-3' : '' }}">

						<a href="javascript:void(0);" class="bill-sort" data-column="vendor_name"
						   style="color:#0070c0;text-decoration:none;">Vendor Name
							<span class="sort-icon">
								@if($sortBy == 'vendor_name')
									{{ $sortDirection == 'asc' ? '▲' : '▼' }}
								@else
									⇅
								@endif
							</span>
						</a>
					</th>

					<th style="background:#fce4d6;color:#0070c0;z-index:999;"
						class="{{ count($billdatalist) > 0 ? 'sticky-col-4' : '' }}">

						<a href="javascript:void(0);" class="bill-sort" data-column="truck_type" style="color:#0070c0;text-decoration:none;">
							Truck type
							<span class="sort-icon">
								@if($sortBy == 'truck_type')
									{{ $sortDirection == 'asc' ? '▲' : '▼' }}
								@else
									⇅
								@endif
							</span>
						</a>
					</th>
					<th style="background:#fce4d6;color:#0070c0;">

						<a href="javascript:void(0);" class="bill-sort" data-column="consignor_name" style="color:#0070c0;text-decoration:none;">Consignor name

							<span class="sort-icon">
								@if($sortBy == 'consignor_name')
									{{ $sortDirection == 'asc' ? '▲' : '▼' }}
								@else
									⇅
								@endif
							</span>
						</a>
					</th>
					<th style="background:#fce4d6;color:#0070c0;">

						<a href="javascript:void(0);" class="bill-sort" data-column="consignor_code" style="color:#0070c0;text-decoration:none;">Consignor<br>code

							<span class="sort-icon">
								@if($sortBy == 'consignor_code')
									{{ $sortDirection == 'asc' ? '▲' : '▼' }}
								@else
									⇅
								@endif
							</span>
						</a>
					</th>
					<th style="background:#fce4d6;color:#0070c0;">

						<a href="javascript:void(0);" class="bill-sort" data-column="consignor_location" style="color:#0070c0;text-decoration:none;">Consignor location
							<span class="sort-icon">
								@if($sortBy == 'consignor_location')
									{{ $sortDirection == 'asc' ? '▲' : '▼' }}
								@else
									⇅
								@endif
							</span>
						</a>
					</th>

					<th style="background:#fce4d6;color:#0070c0;">

						<a href="javascript:void(0);" class="bill-sort" data-column="consignee_name"style="color:#0070c0;text-decoration:none;">Consignee Name
							<span class="sort-icon">
								@if($sortBy == 'consignee_name')
									{{ $sortDirection == 'asc' ? '▲' : '▼' }}
								@else
									⇅
								@endif

							</span>

						</a>

					</th>


					<th style="background:#fce4d6;color:#0070c0;">

						<a href="javascript:void(0);" class="bill-sort" data-column="consignee_code" style="color:#0070c0;text-decoration:none;">Consignee<br>
							Code
							<span class="sort-icon">
								@if($sortBy == 'consignee_code')

									{{ $sortDirection == 'asc' ? '▲' : '▼' }}

								@else

									⇅

								@endif
							</span>
							</a>
					</th>
					<th style="background:#fce4d6;color:#0070c0;">

						<a href="javascript:void(0);" class="bill-sort" data-column="consignee_location" style="color:#0070c0;text-decoration:none;">Consignee Location
						<span class="sort-icon">

								@if($sortBy == 'consignee_location')

									{{ $sortDirection == 'asc' ? '▲' : '▼' }}

								@else

									⇅

								@endif

							</span>
						</a>
					</th>
					<th style="background:#fce4d6;color:#0070c0;">
						<a href="javascript:void(0);" class="bill-sort" data-column="ref1" style="color:#0070c0;text-decoration:none;">
							Ref1
							<span class="sort-icon">
								@if($sortBy == 'ref1')
									{{ $sortDirection == 'asc' ? '▲' : '▼' }}
								@else
									⇅
								@endif
							</span>

						</a>

					</th>


					{{-- Vendor Code --}}
					<th style="background:#fce4d6;color:#0070c0;">

						<a href="javascript:void(0);"
						   class="bill-sort"
						   data-column="vendor_code"
						   style="color:#0070c0;text-decoration:none;">

							Vendor Code

							<span class="sort-icon">

								@if($sortBy == 'vendor_code')

									{{ $sortDirection == 'asc' ? '▲' : '▼' }}

								@else

									⇅

								@endif

							</span>

						</a>

					</th>


					{{-- T Code --}}
					<th style="background:#fce4d6;color:#0070c0;">

						<a href="javascript:void(0);"
						   class="bill-sort"
						   data-column="t_code"
						   style="color:#0070c0;text-decoration:none;">

							T code

							<span class="sort-icon">

								@if($sortBy == 't_code')

									{{ $sortDirection == 'asc' ? '▲' : '▼' }}

								@else

									⇅

								@endif

							</span>

						</a>

					</th>


					{{-- LR No --}}
					<th style="background:#fce4d6;color:#0070c0;">

						<a href="javascript:void(0);"
						   class="bill-sort"
						   data-column="lr_no"
						   style="color:#0070c0;text-decoration:none;">

							LR/CN No.

							<span class="sort-icon">

								@if($sortBy == 'lr_no')

									{{ $sortDirection == 'asc' ? '▲' : '▼' }}

								@else

									⇅

								@endif

							</span>

						</a>

					</th>


					{{-- LR CN Date --}}
					<th style="background:#fce4d6;color:#0070c0;">

						<a href="javascript:void(0);"
						   class="bill-sort"
						   data-column="lr_cn_date"
						   style="color:#0070c0;text-decoration:none;">

							LR CN Date

							<span class="sort-icon">

								@if($sortBy == 'lr_cn_date')

									{{ $sortDirection == 'asc' ? '▲' : '▼' }}

								@else

									⇅

								@endif

							</span>

						</a>

					</th>


					{{-- A Amount --}}
					<th style="background:#fce4d6;color:#0070c0;">

						<a href="javascript:void(0);"
						   class="bill-sort"
						   data-column="a_amount"
						   style="color:#0070c0;text-decoration:none;">

							A amount

							<span class="sort-icon">

								@if($sortBy == 'a_amount')

									{{ $sortDirection == 'asc' ? '▲' : '▼' }}

								@else

									⇅

								@endif

							</span>

						</a>

					</th>


					{{-- Freight PO --}}
					<th style="background:#fce4d6;color:#0070c0;">

						<a href="javascript:void(0);"
						   class="bill-sort"
						   data-column="ref2"
						   style="color:#0070c0;text-decoration:none;">

							Freight PO

							<span class="sort-icon">

								@if($sortBy == 'ref2')

									{{ $sortDirection == 'asc' ? '▲' : '▼' }}

								@else

									⇅

								@endif

							</span>

						</a>

					</th>


					{{-- Freight GRN --}}
					<th style="background:#fce4d6;color:#0070c0;">

						<a href="javascript:void(0);"
						   class="bill-sort"
						   data-column="ref3"
						   style="color:#0070c0;text-decoration:none;">

							Freight GRN

							<span class="sort-icon">

								@if($sortBy == 'ref3')

									{{ $sortDirection == 'asc' ? '▲' : '▼' }}

								@else

									⇅

								@endif

							</span>

						</a>

					</th>


					{{-- Freight Type --}}
					<th style="background:#fce4d6;color:#0070c0;">

						<a href="javascript:void(0);"
						   class="bill-sort"
						   data-column="freight_type"
						   style="color:#0070c0;text-decoration:none;">

							Freight type

							<span class="sort-icon">

								@if($sortBy == 'freight_type')

									{{ $sortDirection == 'asc' ? '▲' : '▼' }}

								@else

									⇅

								@endif

							</span>

						</a>

					</th>


					{{-- AP Status --}}
					<th style="background:#fce4d6;color:#0070c0;">

						<a href="javascript:void(0);"
						   class="bill-sort"
						   data-column="ap_status"
						   style="color:#0070c0;text-decoration:none;">

							Ap status

							<span class="sort-icon">

								@if($sortBy == 'ap_status')

									{{ $sortDirection == 'asc' ? '▲' : '▼' }}

								@else

									⇅

								@endif

							</span>

						</a>

					</th>


					{{-- Created Date --}}
					<th style="background:#fce4d6;color:#0070c0;">

						<a href="javascript:void(0);"
						   class="bill-sort"
						   data-column="created_at"
						   style="color:#0070c0;text-decoration:none;">

							Created_date

							<span class="sort-icon">

								@if($sortBy == 'created_at')

									{{ $sortDirection == 'asc' ? '▲' : '▼' }}

								@else

									⇅

								@endif

							</span>

						</a>

					</th>


					{{-- Return At --}}
					<th style="background:#fce4d6;color:#0070c0;">

						<a href="javascript:void(0);"
						   class="bill-sort"
						   data-column="returned_at"
						   style="color:#0070c0;text-decoration:none;">

							Return at

							<span class="sort-icon">

								@if($sortBy == 'returned_at')

									{{ $sortDirection == 'asc' ? '▲' : '▼' }}

								@else

									⇅

								@endif

							</span>

						</a>

					</th>


					{{-- Submitted At --}}
					<th style="background:#fce4d6;color:#0070c0;">

						<a href="javascript:void(0);"
						   class="bill-sort"
						   data-column="freight_info_updated_at"
						   style="color:#0070c0;text-decoration:none;">

							Submitted at

							<span class="sort-icon">

								@if($sortBy == 'freight_info_updated_at')

									{{ $sortDirection == 'asc' ? '▲' : '▼' }}

								@else

									⇅

								@endif

							</span>

						</a>

					</th>


					{{-- Invoice No --}}
					<th style="background:#fce4d6;color:#0070c0;">

						<a href="javascript:void(0);"
						   class="bill-sort"
						   data-column="freight_invoice_no"
						   style="color:#0070c0;text-decoration:none;">

							Inv No.

							<span class="sort-icon">

								@if($sortBy == 'freight_invoice_no')

									{{ $sortDirection == 'asc' ? '▲' : '▼' }}

								@else

									⇅

								@endif

							</span>

						</a>

					</th>


					{{-- Invoice Date --}}
					<th style="background:#fce4d6;color:#0070c0;">

						<a href="javascript:void(0);"
						   class="bill-sort"
						   data-column="freight_invoice_date"
						   style="color:#0070c0;text-decoration:none;">

							Inv Dt.

							<span class="sort-icon">

								@if($sortBy == 'freight_invoice_date')

									{{ $sortDirection == 'asc' ? '▲' : '▼' }}

								@else

									⇅

								@endif

							</span>

						</a>

					</th>


					{{-- Status --}}
					<th style="background:#fce4d6;color:#0070c0;">

						<a href="javascript:void(0);"
						   class="bill-sort"
						   data-column="status"
						   style="color:#0070c0;text-decoration:none;">

							Status

							<span class="sort-icon">

								@if($sortBy == 'status')

									{{ $sortDirection == 'asc' ? '▲' : '▼' }}

								@else

									⇅

								@endif

							</span>

						</a>

					</th>


					{{-- Action --}}
					<th style="background:#c6e0b4;color:#0070c0;">

						Action

					</th>


					{{-- Bulk Delete Checkbox --}}
					@if($user_role == 1)

						<th style="background:#c6e0b4;color:#0070c0;">

							<input type="checkbox"
								   id="selectAll">

						</th>

					@endif

				</tr>

			</thead>


			<tbody>


				@if(count($billdatalist) > 0)


					@foreach($billdatalist as $billdata)


						<tr>


							<td class="sticky-col-1">

								{{ $billdata->s5_consignor_short_name_and_location }}

							</td>


							<td class="sticky-col-2">

								{{ $billdata->d5_consignor_short_name_and_location }}

							</td>


							<td class="sticky-col-3">

								{{ $billdata->vendor_name }}

							</td>


							<td class="sticky-col-4">

								{{ $billdata->truck_type }}

							</td>


							<td>

								{{ $billdata->consignor_name }}

							</td>


							<td>

								{{ $billdata->consignor_code }}

							</td>


							<td>

								{{ $billdata->consignor_location }}

							</td>


							<td>

								{{ $billdata->consignee_name }}

							</td>


							<td>

								{{ $billdata->consignee_code }}

							</td>


							<td>

								{{ $billdata->consignee_location }}

							</td>


							<td>

								{{ $billdata->ref1 }}

							</td>


							<td>

								{{ $billdata->vendor_code }}

							</td>


							<td>

								{{ $billdata->t_code }}

							</td>


							<td>

								{{ $billdata->lr_no }}

							</td>


							<td>

								{{ $billdata->lr_cn_date }}

							</td>


							<td>

								{{ $billdata->a_amount }}

							</td>


							<td>

								{{ $billdata->ref2 }}

							</td>


							<td>

								{{ $billdata->ref3 }}

							</td>


							<td>

								{{ $billdata->freight_type }}

							</td>


							<td>

								{{ $billdata->ap_status }}

							</td>


							<td>

								{{ $billdata->created_at }}

							</td>


							<td>

								{{ $billdata->returned_at
									? \Carbon\Carbon::parse($billdata->returned_at)->format('Y-m-d')
									: '-'
								}}

							</td>


							<td>

								{{ $billdata->freight_info_updated_at
									? \Carbon\Carbon::parse($billdata->freight_info_updated_at)->format('Y-m-d')
									: '-'
								}}

							</td>


							<td>

								{{ $billdata->freight_invoice_no }}

							</td>


							<td>

								{{ $billdata->freight_invoice_date
									? \Carbon\Carbon::parse($billdata->freight_invoice_date)->format('Y-m-d')
									: '-'
								}}

							</td>


							<td>

								{!! ($billdata->status == 1)
									? "<span class='badge bg-success'>Active</span>"
									: "<span class='badge bg-warning'>Inactive</span>"
								!!}

							</td>


							<td>

								@if($user_role == 1)

									<a class="btn btn-info btn-sm"
									   href="{{ url('admin/billdata/editbilldata/'.$billdata->id) }}">

										<i class="fas fa-pencil-alt"></i>

										Edit

									</a>

								@endif

							</td>


							@if($user_role == 1)

								<td>

									<input type="checkbox"
										   name="ids[]"
										   value="{{ $billdata->id }}"
										   class="row-checkbox">

								</td>

							@endif


						</tr>


					@endforeach


				@else


					<tr>

						<td colspan="{{ $user_role == 1 ? 28 : 27 }}"
							class="text-center">

							No records found.

						</td>

					</tr>


				@endif


			</tbody>

		</table>

	</div>


	{{-- Bulk Delete Button --}}
	@if($user_role == 1)

		<div class="bulk-delete-footer-wrap">

			<div class="bulk-delete-footer-inner"
				 id="bulkDeleteFooterInner">

				@if(count($billdatalist) > 0)

					<button type="submit"
							class="btn btn-danger"
							onclick="return confirmBulkDelete();">

						<i class="fas fa-trash"></i>

						Delete Selected

					</button>

				@endif

			</div>

		</div>


		</form>

	@endif


	<div class="pagination-wrap">
		<div class="row">
			<div class="col-md-6">
				<div class="pagination-info">
					Showing
					<strong>{{ $billdatalist->firstItem() ?? 0 }}</strong>
					to
					<strong>{{ $billdatalist->lastItem() ?? 0 }}</strong>
					of<strong>{{ $billdatalist->total() }}</strong>
					records
				</div>
			</div>
			<div class="col-md-6">
				<div class="float-right">
					{{ $billdatalist->links() }}
				</div>
			</div>
		</div>
	</div>
</div>