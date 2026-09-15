@extends('admin.admin')

@section('bodycontent')

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Edit Material Data</h1>
      </div>

      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item">Home</li>
        </ol>
      </div>
    </div>
  </div>
</div>
<!-- /.content-header -->

<!-- Main content -->
<div class="content">
  <div class="container-fluid">

    <div class="row">
      <div class="col-lg-12">

        <div class="card">

          @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show">
            <strong>{{ session('success') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          @endif

          @if(session('error'))
          <div class="alert alert-warning alert-dismissible fade show">
            <strong>{{ session('error') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          @endif

          <div class="card-body p-0">

            <div class="card card-primary">

              <div class="card-header">
                <h3 class="card-title">Edit Material Data</h3>
              </div>

              <div class="card-body">

                <form action="{{ url('/admin/material/updatematerialdata') }}" method="post" name="addfrm">
                  @csrf

                  <input type="hidden" id="id" name="id" value="{{ $materialdata->id }}">

                  <div class="row">

                    <div class="form-group col-md-6">
                      <label for="material_code">Material Code</label>

                      <input type="text"
                        id="material_code"
                        name="material_code"
                        value="{{ old('material_code', $materialdata->material_code) }}"
                        class="form-control">

                      @error('material_code')
                      <span class="text-danger">{{ $message }}</span>
                      @enderror
                    </div>

                    <div class="form-group col-md-6">
                      <label for="material_description">Material Description</label>

                      <input type="text"
                        id="material_description"
                        name="material_description"
                        value="{{ old('material_description', $materialdata->material_description) }}"
                        class="form-control">

                      @error('material_description')
                      <span class="text-danger">{{ $message }}</span>
                      @enderror
                    </div>

                    <div class="form-group col-md-6">
                      <label for="uom">UOM</label>

                      <input type="text"
                        id="uom"
                        name="uom"
                        value="{{ old('uom', $materialdata->uom) }}"
                        class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="division">Division</label>

                      <input type="text"
                        id="division"
                        name="division"
                        value="{{ old('division', $materialdata->division) }}"
                        class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="piece_per_box">Piece Per Box</label>

                      <input type="number"
                        step="any"
                        id="piece_per_box"
                        name="piece_per_box"
                        value="{{ old('piece_per_box', $materialdata->piece_per_box) }}"
                        class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="length_cm">Length (CM)</label>

                      <input type="number"
                        step="any"
                        id="length_cm"
                        name="length_cm"
                        value="{{ old('length_cm', $materialdata->length_cm) }}"
                        class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="width_cm">Width (CM)</label>

                      <input type="number"
                        step="any"
                        id="width_cm"
                        name="width_cm"
                        value="{{ old('width_cm', $materialdata->width_cm) }}"
                        class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="height_cm">Height (CM)</label>

                      <input type="number"
                        step="any"
                        id="height_cm"
                        name="height_cm"
                        value="{{ old('height_cm', $materialdata->height_cm) }}"
                        class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="net_weight_kg">Net Weight (KG)</label>

                      <input type="number"
                        step="any"
                        id="net_weight_kg"
                        name="net_weight_kg"
                        value="{{ old('net_weight_kg', $materialdata->net_weight_kg) }}"
                        class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="gross_weight_kg">Gross Weight (KG)</label>

                      <input type="number"
                        step="any"
                        id="gross_weight_kg"
                        name="gross_weight_kg"
                        value="{{ old('gross_weight_kg', $materialdata->gross_weight_kg) }}"
                        class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="volume_cft">Volume (CFT)</label>

                      <input type="number"
                        step="any"
                        id="volume_cft"
                        name="volume_cft"
                        value="{{ old('volume_cft', $materialdata->volume_cft) }}"
                        class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="category">Category</label>

                      <input type="text"
                        id="category"
                        name="category"
                        value="{{ old('category', $materialdata->category) }}"
                        class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="pallets">Pallets</label>

                      <input type="text"
                        id="pallets"
                        name="pallets"
                        value="{{ old('pallets', $materialdata->pallets) }}"
                        class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="brand">Brand</label>

                      <input type="text"
                        id="brand"
                        name="brand"
                        value="{{ old('brand', $materialdata->brand) }}"
                        class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="sub_brand">Sub Brand</label>

                      <input type="text"
                        id="sub_brand"
                        name="sub_brand"
                        value="{{ old('sub_brand', $materialdata->sub_brand) }}"
                        class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="thickness_load">Thickness Load</label>

                      <input type="text"
                        id="thickness_load"
                        name="thickness_load"
                        value="{{ old('thickness_load', $materialdata->thickness_load) }}"
                        class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="sequence">Sequence</label>

                      <input type="text"
                        id="sequence"
                        name="sequence"
                        value="{{ old('sequence', $materialdata->sequence) }}"
                        class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="associated">Associated</label>

                      <input type="text"
                        id="associated"
                        name="associated"
                        value="{{ old('associated', $materialdata->associated) }}"
                        class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="parent">Parent</label>

                      <input type="text"
                        id="parent"
                        name="parent"
                        value="{{ old('parent', $materialdata->parent) }}"
                        class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="child">Child</label>

                      <input type="text"
                        id="child"
                        name="child"
                        value="{{ old('child', $materialdata->child) }}"
                        class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="mrp">MRP</label>

                      <input type="number"
                        step="0.01"
                        id="mrp"
                        name="mrp"
                        value="{{ old('mrp', $materialdata->mrp) }}"
                        class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="recovery_mrp">Recovery MRP</label>

                      <input type="number"
                        step="0.01"
                        id="recovery_mrp"
                        name="recovery_mrp"
                        value="{{ old('recovery_mrp', $materialdata->recovery_mrp) }}"
                        class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="inputStatus">Status</label>

                      <select id="inputStatus"
                        name="status"
                        class="form-control custom-select">

                        <option value="1"
                          {{ old('status', $materialdata->status) == 1 ? 'selected' : '' }}>
                          Active
                        </option>

                        <option value="0"
                          {{ old('status', $materialdata->status) == 0 ? 'selected' : '' }}>
                          Inactive
                        </option>

                      </select>
                    </div>

                  </div>

                  <div class="form-group">

                    <button type="submit"
                      name="submit"
                      class="btn btn-primary">
                      Update Material Data
                    </button>

                    <a href="{{ url('/admin/materialdata/material-data-list') }}"
                      class="btn btn-secondary">
                      Back
                    </a>

                  </div>

                </form>

              </div>
              <!-- /.card-body -->

            </div>

          </div>

        </div>

      </div>
    </div>

  </div>
</div>
<!-- /.content -->

@endsection