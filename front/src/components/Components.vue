<template>
  <div class="container-fluid">
    <b-card no-block style="margin-top:-20px">
      <b-tabs small card ref="tabs">
        <!-- MANAGE WAREHOUSE -->
        <b-tab title="Warehouses" style="cursor:pointer">
          <b-card>
            <b>Manage Warehouses</b>
            <br />
            <div class="col-md-6" style="display:flex">
              <div class="form-group" style="float:left">
                <div class="form-line">
                  <input
                    type="text"
                    class="form-control"
                    autocomplete="off"
                    v-model="search.warehouse"
                    @keyup="searchKeyword"
                    placeholder="Search . ."
                  />
                </div>
              </div>
              <i
                style="float:right;margin-top:8px"
                class="material-icons"
                @click="searchKeyword"
                >search</i
              >
            </div>
            <button
              type="button"
              class="btn btn-default waves-effect"
              @click="createNewWarehouse"
              v-show="roles.create_comp"
              style="float:right"
            >
              <i class="material-icons">note_add</i>
              <span>Create</span>
            </button>
          </b-card>
          <b-card-text>
            <div class="row clearfix" style="margin-top:-25px">
              <div class="col-md-12">
                <div class="table-wrap">
                  <div class="table-responsive">
                    <table
                      class="table table-striped table-condensed table-hover"
                      id="warehouseTable"
                      ref="warehouseTable"
                    >
                      <thead>
                        <tr>
                          <th>Warehouse Name</th>
                          <th>Address</th>
                          <th>Created at</th>
                          <th>Updated at</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="(warehouse, index) in warehouses"
                          :key="warehouse.index"
                          style="cursor: pointer;"
                          @click="getWarehouse(index)"
                          data-toggle="modal"
                          data-target="#warehouseModal"
                        >
                          <td>{{ warehouse.name }}</td>
                          <td>
                            <small>
                              <i>{{ warehouse.address }}</i>
                            </small>
                          </td>
                          <td>
                            <small>{{ warehouse.created_at }}</small>
                          </td>
                          <td>
                            <small>{{ warehouse.updated_at }}</small>
                          </td>
                        </tr>
                        <tr v-show="warehouses.length == 0">
                          <td colspan="4" class="text-center col-red">
                            <small>
                              <i>No warehouse found.</i>
                            </small>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <p>{{ warehouses.length }} warehouses found.</p>
              </div>
            </div>
          </b-card-text>
        </b-tab>

        <!-- MANAGE CATEGORY -->
        <b-tab title="Category" style="cursor:pointer">
          <b-card>
            <b>Manage Categories</b>
            <br />
            <div class="col-md-6" style="display:flex">
              <div class="form-group" style="float:left">
                <div class="form-line">
                  <input
                    type="text"
                    class="form-control"
                    autocomplete="off"
                    v-model="search.category"
                    @keyup="searchKeyword"
                    placeholder="Search . . ."
                  />
                </div>
              </div>
              <i
                style="float:right;margin-top:8px"
                class="material-icons"
                @click="searchKeyword"
                >search</i
              >
            </div>
            <button
              type="button"
              class="btn btn-default waves-effect"
              v-show="roles.create_comp"
              style="float:right"
              @click="createNewCategory"
            >
              <i class="material-icons">note_add</i>
              <span>Create</span>
            </button>
          </b-card>
          <b-card-text>
            <div class="row clearfix" style="margin-top:-25px">
              <div class="col-md-12">
                <div class="table-wrap">
                  <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                      <tbody>
                        <tr
                          v-for="(category, index) in categories"
                          :key="category.index"
                          style="cursor: pointer;"
                          @click="getCategory(index)"
                          data-toggle="modal"
                          data-target="#categoryModal"
                        >
                          <td>{{ category.name }}</td>
                        </tr>
                        <tr v-show="categories.length == 0">
                          <td colspan="5" class="text-center">
                            <small class="col-red">
                              <i>No category found.</i>
                            </small>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </b-card-text>
        </b-tab>

        <!-- MANAGE ASSET TYPE -->
        <b-tab title="Asset Type" style="cursor:pointer">
          <b-card>
            <b> Manage Asset Types</b>
            <br />
            <div class="col-md-6" style="display:flex">
              <div class="form-group" style="float:left">
                <div class="form-line">
                  <input
                    type="text"
                    class="form-control"
                    autocomplete="off"
                    v-model="search.type"
                    @keyup="searchKeyword"
                    placeholder="Search . . ."
                  />
                </div>
              </div>
              <i
                style="float:right;margin-top:8px"
                class="material-icons"
                @click="searchKeyword"
                >search</i
              >
            </div>
            <button
              type="button"
              class="btn btn-default waves-effect"
              style="float:right"
              data-toggle="modal"
              data-target="#modalAddType"
              v-show="roles.create_comp"
            >
              <i class="material-icons">note_add</i>
              <span>Create</span>
            </button>
          </b-card>
          <b-card-text>
            <div class="row clearfix" style="margin-top:-25px">
              <div class="col-md-12">
                <div class="table-wrap">
                  <div class="table-responsive">
                    <table
                      class="table table-striped table-condensed table-hover"
                    >
                      <thead>
                        <tr>
                          <th>Name</th>
                          <th>Date Created</th>
                          <th>Updated At</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="(type, index) in types"
                          :key="type.index"
                          style="cursor: pointer;"
                          @click="getType(index)"
                          data-toggle="modal"
                          data-target="#assetTypeModal"
                        >
                          <td>{{ type.type_name }}</td>
                          <td>{{ type.created_at }}</td>
                          <td>{{ type.updated_at }}</td>
                        </tr>
                        <tr v-show="type.length == 0">
                          <td colspan="5" class="text-center">
                            <small class="col-red">
                              <i>No asset type found.</i>
                            </small>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </b-card-text>
        </b-tab>

        <!-- MANAGE COMPANY ASSETS -->
        <b-tab title="Company Assets" style="cursor:pointer">
          <b-card>
            <b>Manage Company Assets</b>
            <br />
            <div class="col-md-6" style="display:flex">
              <div class="form-group" style="float:left">
                <div class="form-line">
                  <input
                    type="text"
                    class="form-control"
                    autocomplete="off"
                    v-model="search.asset"
                    @keyup="searchKeyword"
                    placeholder="Search . . ."
                  />
                </div>
              </div>
              <i
                style="float:right;margin-top:8px"
                class="material-icons"
                @click="searchKeyword"
                >search</i
              >
            </div>
            <button
              type="button"
              class="btn btn-default waves-effect"
              style="float:right"
              v-show="roles.create_comp"
              @click="createCompanyAsset"
            >
              <i class="material-icons">note_add</i>
              <span>Create</span>
            </button>
          </b-card>
          <b-card-text>
            <div class="row clearfix" style="margin-top:-25px">
              <div class="col-md-12">
                <!-- START ORDER LIST TABLE -->
                <div class="table-wrap">
                  <div class="table-responsive">
                    <table
                      class="table table-striped table-condensed table-hover"
                    >
                      <thead>
                        <tr>
                          <th>Name</th>
                          <th>Type</th>
                          <th>Model</th>
                          <th>Area</th>
                          <th>Date in</th>
                          <th>Accountable</th>
                          <th>Date Accounted</th>
                          <th>Remarks</th>
                        </tr>
                      </thead>
                      <tbody>
                        <router-link
                          tag="tr"
                          :to="'/company_assets/' + asset.id + '/edit'"
                          v-for="asset in assets"
                          :key="asset.id"
                          style="cursor: pointer;"
                        >
                          <!-- <tr
                      v-for="(asset, index) in assets"
                      :key="asset.index"
                      style="cursor: pointer;"
                      @click="getCompanyAssets(index)"
                      data-toggle="modal"
                      data-target="#companyAssetModal"
                    > -->
                          <td>{{ asset.name }}</td>
                          <td>{{ asset.type_name }}</td>
                          <td>{{ asset.model }}</td>
                          <td>{{ asset.area }}</td>
                          <td>{{ asset.date_in }}</td>
                          <td>{{ asset.accountable_name }}</td>
                          <td>{{ asset.date_accounted }}</td>
                          <td>{{ asset.remarks }}</td>
                          <!-- </tr> -->
                        </router-link>

                        <tr v-show="assets.length == 0">
                          <td colspan="8" class="text-center">
                            <small class="col-red">
                              <i>No assets found.</i>
                            </small>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <!-- END ORDER LIST TABLE-->
                <br />
                <p>{{ assets.length }} orders found.</p>
              </div>
            </div>
          </b-card-text>
        </b-tab>

        <!--  MANAGE MOP-->
        <b-tab title="Mode of Payment" style="cursor:pointer">
          <b-card>
            <b>Manage Mode of Payment</b>
            <br />
            <div class="col-md-6" style="display:flex">
              <div class="form-group" style="float:left">
                <div class="form-line">
                  <input
                    type="text"
                    class="form-control"
                    autocomplete="off"
                    v-model="search.mode"
                    @keyup="searchKeyword"
                    placeholder="Search . . ."
                  />
                </div>
              </div>
              <i
                style="float:right;margin-top:8px"
                class="material-icons"
                @click="searchKeyword"
                >search</i
              >
            </div>
            <button
              type="button"
              class="btn btn-default waves-effect"
              style="float:right"
              data-toggle="modal"
              data-target="#modalAddMOP"
              v-show="roles.create_comp"
            >
              <i class="material-icons">note_add</i>
              <span>Create</span>
            </button>
          </b-card>
          <b-card-text>
            <div class="row clearfix" style="margin-top:-25px">
              <div class="col-md-12">
                <!-- START ORDER LIST TABLE -->
                <div class="table-wrap">
                  <div class="table-responsive">
                    <table
                      class="table table-striped table-condensed table-hover"
                      id="MOPTable"
                    >
                      <thead>
                        <tr>
                          <th>Name</th>
                          <th>Date Created</th>
                          <th>Updated At</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="(mode, index) in modes"
                          :key="mode.index"
                          style="cursor: pointer;"
                          @click="getMode(index)"
                          data-toggle="modal"
                          data-target="#modalEditMOP"
                        >
                          <td>{{ mode.mode }}</td>
                          <td>{{ mode.created_at }}</td>
                          <td>{{ mode.updated_at }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <!-- END ORDER LIST TABLE-->
                <br />
                <p>{{ modes.length }} data found.</p>
              </div>
            </div>
          </b-card-text>
        </b-tab>

        <!-- MANAGE TERM -->
        <b-tab title="Term" style="cursor:pointer">
          <b-card>
            <b>Manage Term</b>
            <br />
            <div class="col-md-6" style="display:flex">
              <div class="form-group" style="float:left">
                <div class="form-line">
                  <input
                    type="text"
                    class="form-control"
                    autocomplete="off"
                    v-model="search.term"
                    @keyup="searchKeyword"
                    placeholder="Search . . ."
                  />
                </div>
              </div>
              <i
                style="float:right;margin-top:8px"
                class="material-icons"
                @click="searchKeyword"
                >search</i
              >
            </div>
            <button
              type="button"
              class="btn btn-default waves-effect"
              style="float:right"
              data-toggle="modal"
              data-target="#modalAddTerm"
              v-show="roles.create_comp"
            >
              <i class="material-icons">note_add</i>
              <span>Create</span>
            </button>
          </b-card>
          <b-card-text>
            <div class="row clearfix" style="margin-top:-25px">
              <div class="col-md-12">
                <!-- START ORDER LIST TABLE -->
                <div class="table-wrap">
                  <div class="table-responsive">
                    <table
                      class="table table-striped table-condensed table-hover"
                      id="MOPTable"
                    >
                      <thead>
                        <tr>
                          <th>Term</th>
                          <th>Date Created</th>
                          <th>Updated At</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="(term, index) in terms"
                          :key="term.index"
                          style="cursor: pointer;"
                          @click="getTerm(index)"
                          data-toggle="modal"
                          data-target="#modalEditTerm"
                        >
                          <td>{{ term.term }}</td>
                          <td>{{ term.created_at }}</td>
                          <td>{{ term.updated_at }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <!-- END ORDER LIST TABLE-->
                <br />
                <p>{{ terms.length }} data found.</p>
              </div>
            </div>
          </b-card-text>
        </b-tab>
      </b-tabs>
    </b-card>

    <!----------------------------------------------- MODALS FOR COMPONENTS --------------------------------->

    <!-- Modal ADD WAREHOUSE -->
    <div class="modal fade" id="modalAddWarehouse" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" style="display:block">
            <button type="button" class="close" data-dismiss="modal">
              &times;
            </button>
            <h4 class="modal-title">Add Warehouse</h4>
          </div>
          <div class="modal-body">
            <form @submit.prevent="createWarehouse">
              <div class="body">
                <div class="row clearfix">
                  <div class="col-lg-12 col-md-12">
                    <span>Warehouse Name</span>
                    <div class="input-group">
                      <div class="form-line">
                        <input
                          ref="warehouse_name"
                          name="warehouse_name"
                          type="text"
                          class="form-control"
                          v-validate="'required'"
                          v-model.trim="warehouseNew.name"
                          autocomplete="off"
                        />
                      </div>
                      <small
                        class="text-danger pull-left"
                        v-show="errors.has('warehouse_name')"
                        >Warehouse name is required.</small
                      >
                    </div>
                  </div>
                </div>
                <div class="row clearfix">
                  <div class="col-lg-12">
                    <label>Address</label>
                    <div class="input-group">
                      <div class="form-line">
                        <textarea
                          name="warehouse_address"
                          type="warehouse_address"
                          class="form-control"
                          v-model.trim="warehouseNew.address"
                          rows="5"
                        ></textarea>
                      </div>
                      <small
                        class="text-danger pull-left"
                        v-show="errors.has('warehouse_address')"
                        >Warehouse address is required.</small
                      >
                    </div>
                  </div>
                </div>

                <div class="row clearfix">
                  <div
                    class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-lg-offset-6"
                  >
                    <input
                      type="submit"
                      value="Create"
                      class="btn btn-lg btn-info waves-effect waves-light pull-right"
                    />
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer"></div>
        </div>
      </div>
    </div>
    <!--  -->

    <!-- Modal ADD ASSET TYPE-->
    <div class="modal fade" id="modalAddType" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" style="display:block">
            <button type="button" class="close" data-dismiss="modal">
              &times;
            </button>
            <h4 class="modal-title">Add Asset type</h4>
          </div>
          <div class="modal-body">
            <form @submit.prevent="createType">
              <div class="body">
                <div class="row clearfix">
                  <div class="col-lg-12 col-md-12">
                    <span>Asset type Name</span>
                    <div class="input-group">
                      <div class="form-line">
                        <input
                          ref="type_name"
                          name="type_name"
                          type="text"
                          class="form-control"
                          v-validate="'required'"
                          v-model.trim="addType.type_name"
                          autocomplete="off"
                        />
                      </div>
                      <small
                        class="text-danger pull-left"
                        v-show="errors.has('type_name')"
                        >Asset type name is required.</small
                      >
                    </div>
                  </div>
                </div>

                <div class="row clearfix">
                  <div
                    class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-lg-offset-6"
                  >
                    <input
                      type="submit"
                      value="Create"
                      class="btn btn-lg btn-info waves-effect waves-light pull-right"
                      style="float:right"
                    />
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer"></div>
        </div>
      </div>
    </div>

    <!-- UPDATE WAREHOUSE MODAL -->
    <div
      id="warehouseModal"
      class="modal fade"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
      data-keyboard="false"
    >
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <b>
              Warehouse
            </b>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form @submit.prevent="updateWarehouse">
            <div class="modal-body">
              <div class="row clearfix">
                <div class="col-sm-12">
                  <label>Name</label>
                  <div class="input-group">
                    <div class="form-line">
                      <input
                        type="text"
                        ref="warehouseName"
                        name="warehouseName"
                        class="form-control"
                        v-validate="'required'"
                        v-model="warehouse.name"
                        autocomplete="off"
                        autofocus="on"
                        :disabled="!roles.update_comp"
                      />
                    </div>
                    <small
                      class="text-danger pull-left"
                      v-show="errors.has('warehouseName')"
                      >Warehouse name is required.</small
                    >
                  </div>
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-sm-12">
                  <label>Address</label>
                  <div class="input-group">
                    <div class="form-line">
                      <textarea
                        type="text"
                        class="form-control"
                        v-model="warehouse.address"
                        rows="2"
                        :disabled="!roles.update_comp"
                      ></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <input
                type="submit"
                value="Save Changes"
                class="btn btn-lg btn-info waves-effect waves-light pull-right"
                v-show="roles.update_comp"
              />
            </div>
          </form>
        </div>
      </div>
    </div>

    <!--UPDATE CATEGORY MODAL -->
    <div
      id="categoryModal"
      class="modal fade"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
      data-keyboard="false"
    >
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <b> Category</b>

            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form @submit.prevent="updateCategory">
            <div class="modal-body">
              <div class="row clearfix">
                <div class="col-sm-12">
                  <label>Category</label>
                  <div class="input-group">
                    <div class="form-line">
                      <input
                        type="text"
                        ref="categoryName"
                        name="categoryName"
                        class="form-control"
                        v-validate="'required'"
                        v-model="category.name"
                        autocomplete="off"
                        autofocus="on"
                        :disabled="!roles.update_comp"
                      />
                    </div>
                    <small
                      class="text-danger pull-left"
                      v-show="errors.has('categoryName')"
                      >Category name is required.</small
                    >
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <input
                type="submit"
                value="Save Changes"
                class="btn btn-lg btn-info waves-effect waves-light pull-right"
                v-show="roles.update_comp"
              />
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- UPDATE ASSET TYPE MODAL -->
    <div
      id="assetTypeModal"
      class="modal fade"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
      data-keyboard="false"
    >
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <b>Update Asset Type</b>

            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form @submit.prevent="updateType">
            <div class="modal-body">
              <div class="row clearfix">
                <div class="col-sm-12">
                  <label>Asset Type</label>
                  <div class="input-group">
                    <div class="form-line">
                      <input
                        type="text"
                        ref="typeName"
                        name="typeName"
                        class="form-control"
                        v-validate="'required'"
                        v-model="type.type_name"
                        autocomplete="off"
                        autofocus="on"
                        :disabled="!roles.update_comp"
                      />
                    </div>
                    <small
                      class="text-danger pull-left"
                      v-show="errors.has('typeName')"
                      >Category name is required.</small
                    >
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <input
                type="submit"
                value="Save Changes"
                class="btn btn-lg btn-info waves-effect waves-light pull-right"
                v-show="roles.update_comp"
              />
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- END ASSET TYPE MODAL -->

    <!-- UPDATE COMPANY ASSET MODAL -->
    <div
      id="companyAssetModal"
      class="modal fade"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
      data-keyboard="false"
    >
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <b>Update Asset</b>

            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form @submit.prevent="updateAsset">
            <div class="modal-body">
              <div class="row clearfix">
                <div class="col-sm-6">
                  <span>Date In</span>
                  <date-picker
                    v-model="asset.date_in"
                    :config="options"
                    v-validate="'required'"
                    name="date_in"
                    autocomplete="off"
                  ></date-picker>

                  <small
                    class="text-danger pull-left"
                    v-show="errors.has('date_in')"
                    >Date is required.</small
                  >
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-sm-6">
                  <br />
                  <span>Asset Name</span>

                  <input
                    ref="name"
                    name="name"
                    type="text"
                    class="form-control"
                    v-validate="'required'"
                    v-model.trim="asset.name"
                    autocomplete="off"
                    autofocus="on"
                  />

                  <small
                    class="text-danger pull-left"
                    v-show="errors.has('name')"
                    >Asset name is required.</small
                  >
                </div>

                <div class="col-sm-6">
                  <br />
                  <span>Model</span>

                  <input
                    ref="model"
                    name="model"
                    type="text"
                    class="form-control"
                    v-validate="'required'"
                    v-model.trim="asset.model"
                    autocomplete="off"
                    autofocus="on"
                  />

                  <small
                    class="text-danger pull-left"
                    v-show="errors.has('model')"
                    >Model is required.</small
                  >
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-sm-6">
                  <br />
                  <span>Area</span>

                  <input
                    ref="area"
                    name="area"
                    type="text"
                    class="form-control"
                    v-validate="'required'"
                    v-model.trim="asset.area"
                    autocomplete="off"
                    autofocus="on"
                  />

                  <small
                    class="text-danger pull-left"
                    v-show="errors.has('area')"
                    >Area name is required.</small
                  >
                </div>

                <div class="col-sm-6">
                  <br />
                  <span>Type</span>

                  <select
                    class="form-control"
                    ref="type"
                    name="type"
                    v-model="asset.company_assets_types_id"
                  >
                    <option
                      v-for="type in types"
                      :key="type.id"
                      v-bind:value="type.id"
                      >{{ type.type_name }}</option
                    >
                  </select>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-sm-12">
                  <br />
                  <span>Note/Remarks</span>

                  <textarea
                    type="text"
                    class="form-control"
                    rows="5"
                    name="remarks"
                    v-model="asset.remarks"
                  ></textarea>
                </div>
              </div>

              <div class="row clearfix">
                <div class="header">
                  <h5>Accountable</h5>
                </div>
                <div class="col-sm-6">
                  <br />
                  <span>Accountable name</span>

                  <input
                    type="text"
                    class="form-control"
                    v-model="asset.accountable_name"
                    autocomplete="off"
                    ref="accountable"
                    name="accountable"
                    autofocus="on"
                  />
                </div>

                <div class="col-sm-6">
                  <br />
                  <span>Date Accounted</span>
                  <date-picker
                    v-model="asset.date_accounted"
                    :config="options"
                    name="accounted_date"
                    autocomplete="off"
                  ></date-picker>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <input
                v-show="roles.update_comp"
                type="submit"
                value="Save Changes"
                class="btn btn-lg btn-info waves-effect waves-light pull-right"
              />
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- ADD MOP MODAL -->
    <div class="modal fade" id="modalAddMOP" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" style="display:block">
            <button type="button" class="close" data-dismiss="modal">
              &times;
            </button>
            <h4 class="modal-title">Add Mode of Payment</h4>
          </div>
          <div class="modal-body">
            <form @submit.prevent="createMOP">
              <div class="body">
                <div class="row clearfix">
                  <div class="col-lg-12 col-md-12">
                    <span>Mode of Payment</span>
                    <div class="input-group">
                      <div class="form-line">
                        <input
                          ref="mop"
                          name="mop"
                          type="text"
                          class="form-control"
                          autocomplete="off"
                          v-validate="'required'"
                          v-model.trim="addMode.name"
                        />
                      </div>
                      <small
                        class="text-danger pull-left"
                        v-show="errors.has('mop')"
                        >Input is required.</small
                      >
                    </div>
                  </div>
                </div>

                <div class="row clearfix">
                  <div
                    class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-lg-offset-6"
                  >
                    <input
                      type="submit"
                      value="Create"
                      class="btn btn-lg btn-info waves-effect waves-light pull-right"
                      style="float:right"
                    />
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer"></div>
        </div>
      </div>
    </div>
    <!-- UPDATE MOP MODAL -->
    <div
      id="modalEditMOP"
      class="modal fade"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
      data-keyboard="false"
    >
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Update Mode of Payment</h4>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="updateMOP">
              <div class="body">
                <div class="row clearfix">
                  <div class="col-lg-12 col-md-12">
                    <span>Mode of Payment</span>
                    <div class="input-group">
                      <div class="form-line">
                        <input
                          ref="editMOP"
                          name="editMOP"
                          type="text"
                          class="form-control"
                          autocomplete="off"
                          v-validate="'required'"
                          v-model.trim="mode.mode"
                          :disabled="!roles.update_comp"
                        />
                      </div>
                      <small
                        class="text-danger pull-left"
                        v-show="errors.has('editMOP')"
                        >Input is required.</small
                      >
                    </div>
                  </div>
                </div>

                <div class="row clearfix">
                  <div
                    class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-lg-offset-6"
                  >
                    <input
                      type="submit"
                      value="Save Changes"
                      class="btn btn-lg btn-info waves-effect waves-light pull-right"
                      style="float:right"
                      v-show="roles.update_comp"
                    />
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer"></div>
        </div>
      </div>
    </div>

    <!-- ADD TERM MODAL -->
    <div class="modal fade" id="modalAddTerm" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" style="display:block">
            <button type="button" class="close" data-dismiss="modal">
              &times;
            </button>
            <h4 class="modal-title">Add Term</h4>
          </div>
          <div class="modal-body">
            <form @submit.prevent="createTerm">
              <div class="body">
                <div class="row clearfix">
                  <div class="col-lg-12 col-md-12">
                    <span>Term</span>
                    <div class="input-group">
                      <div class="form-line">
                        <input
                          ref="term"
                          name="term"
                          type="text"
                          class="form-control"
                          autocomplete="off"
                          v-validate="'required'"
                          v-model.trim="addTerm.name"
                        />
                      </div>
                      <small
                        class="text-danger pull-left"
                        v-show="errors.has('term')"
                        >Input is required.</small
                      >
                    </div>
                  </div>
                </div>

                <div class="row clearfix">
                  <div
                    class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-lg-offset-6"
                  >
                    <input
                      type="submit"
                      value="Create"
                      class="btn btn-lg btn-info waves-effect waves-light pull-right"
                      style="float:right"
                    />
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer"></div>
        </div>
      </div>
    </div>
    <!-- UPDATE TERM MODAL -->
    <div
      id="modalEditTerm"
      class="modal fade"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
      data-keyboard="false"
    >
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Update Term</h4>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="updateTerm">
              <div class="body">
                <div class="row clearfix">
                  <div class="col-lg-12 col-md-12">
                    <span>Term</span>
                    <div class="input-group">
                      <div class="form-line">
                        <input
                          ref="editTerm"
                          name="editTerm"
                          type="text"
                          class="form-control"
                          autocomplete="off"
                          v-validate="'required'"
                          v-model.trim="term.term"
                          :disabled="!roles.update_comp"
                        />
                      </div>
                      <small
                        class="text-danger pull-left"
                        v-show="errors.has('editTerm')"
                        >Input is required.</small
                      >
                    </div>
                  </div>
                </div>

                <div class="row clearfix">
                  <div
                    class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-lg-offset-6"
                  >
                    <input
                      type="submit"
                      value="Save Changes"
                      class="btn btn-lg btn-info waves-effect waves-light pull-right"
                      style="float:right"
                      v-show="roles.update_comp"
                    />
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import swal from "sweetalert";

import datePicker from "vue-bootstrap-datetimepicker";
import "pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css";

export default {
  components: {
    datePicker
  },

  data() {
    return {
      warehouses: [],
      warehouse: [],
      categories: [],
      category: {},
      types: [],
      type: {},
      mode: {},
      term: {},
      assets: [],
      roles: [],
      tabs: [],
      tabCounter: 0,
      asset: {
        accountable_name: "",
        area: "",
        company_assets_types_id: "",
        created_at: "",
        date_accounted: "",
        date_in: "",
        model: "",
        name: "",
        remarks: "",
        updated_at: "",
        id: ""
      },
      options: {
        format: "YYYY-MM-DD",
        useCurrent: false
      },
      warehouseNew: {
        name: "",
        address: ""
      },
      addType: {
        type_name: ""
      },
      addMode: {
        name: ""
      },
      modes: [],
      terms: [],
      addTerm: {
        name: ""
      },
      search: {
        warehouse: "",
        category: "",
        type: "",
        asset: "",
        mode: "",
        term: ""
      }
    };
  },

  mounted() {
    this.loadData();
  },
  created() {
    this.warehouses = this.$global.getWarehouses();
    this.categories = this.$global.getCategories();
    this.assets = this.$global.getCompanyAssets();
    this.roles = this.$global.getRoles();
    this.loadTypes();
    this.loadMode();
    this.loadTerms();
  },

  methods: {
    getWarehouse(index) {
      this.warehouse = this.warehouses[index];
    },

    createNewWarehouse() {
      swal("Add a new warehouse?", {
        buttons: {
          Yes: true,
          cancel: "Cancel"
        }
      }).then(value => {
        switch (value) {
          case "Yes":
            this.$router.push({
              path: "/create/warehouse"
            });
            break;

          default:
            break;
        }
      });
    },
    updateWarehouse() {
      if (this.warehouse.name != null) {
        this.$http
          .put("api/warehouse/" + this.warehouse.id, this.warehouse)
          .then(response => {
            this.warehouses = response.body;
            this.$global.setWarehouses(response.body);
            swal(this.warehouse.name, "was successfully created!", {
              icon: "success"
            });

            this.warehouse = {};
          })
          .catch(response => {
            swal({
              title: "Error",
              text: response.body.error,
              icon: "error",
              dangerMode: true
            }).then(value => {
              if (value) {
              }
            });
          });
      } else {
        swal("Please complete the details.", "Information", "info");
      }
    },

    getCategory(index) {
      this.category = this.categories[index];
    },

    createNewCategory() {
      swal("Add a new category?", {
        buttons: {
          Yes: true,
          cancel: "Cancel"
        }
      }).then(value => {
        switch (value) {
          case "Yes":
            this.$router.push({
              path: "/create/category"
            });
            break;

          default:
            break;
        }
      });
    },

    updateCategory() {
      if (this.category.name != null) {
        this.$http
          .put("api/category/" + this.category.id, this.category)
          .then(response => {
            this.categories = response.body;
            this.$global.setCategories(response.body);
            swal(this.category.name, "has successfully updated!", {
              icon: "success"
            });

            this.category = {};
          })
          .catch(response => {
            swal({
              title: "Error",
              text: response.body.error,
              icon: "error",
              dangerMode: true
            }).then(value => {
              if (value) {
              }
            });
          });
      } else {
        swal("Category name is required.", "Information", "info");
      }
    },

    getType(index) {
      this.type = this.types[index];
    },

    loadTypes() {
      this.$http.get("api/company_assets_type").then(response => {
        this.types = response.body;
      });
    },

    loadMode() {
      this.$http.get("api/mode_of_payment").then(response => {
        this.modes = response.body;
      });
    },

    loadTerms() {
      this.$http.get("api/term").then(response => {
        this.terms = response.body;
      });
    },

    createType() {
      if (this.addType.type_name != "") {
        swal("Create new Asset type?", {
          buttons: {
            Yes: true,
            cancel: "Cancel"
          }
        }).then(value => {
          switch (value) {
            case "Yes":
              this.$http
                .post("api/company_assets_type", this.addType)
                .then(response => {
                  this.loadTypes();
                  swal(
                    this.addType.type_name,
                    "has successfully added!",
                    "success"
                  );
                })
                //   break;
                .catch(response => {
                  swal({
                    title: "Error",
                    text: response.body.error,
                    icon: "error",
                    dangerMode: true
                  }).then(value => {
                    if (value) {
                      this.$refs.name.focus();
                    }
                  });
                });

            default:
              break;
          }
        });
      } else {
        swal("Asset type name is required.", "Information", "info");
      }
    },

    updateType() {
      if (this.type.type_name != null) {
        this.$http
          .put("api/company_assets_type/" + this.type.id, this.type)
          .then(response => {
            this.loadTypes();
            swal(this.type.type_name, "has successfully updated!", {
              icon: "success"
            });

            this.type = {};
          })
          .catch(response => {
            swal({
              title: "Error",
              text: response.body.error,
              icon: "error",
              dangerMode: true
            }).then(value => {
              if (value) {
              }
            });
          });
      } else {
        swal("type name is required.", "Information", "info");
      }
    },

    loadData() {
      this.$http.get("api/company_assets/").then(response => {
        // console.log(response.body);
        // this.assets = response.body;
        this.asset.accountable_name = response.body.accountable_name;
        this.asset.area = response.body.area;
        this.asset.company_assets_types_id =
          response.body.company_assets_types_id;
        this.asset.created_at = response.body.created_at;
        this.asset.date_accounted = response.body.date_accounted;
        this.asset.date_in = response.body.date_in;
        this.asset.id = response.body.id;
        this.asset.model = response.body.model;
        this.asset.name = response.body.name;
        this.asset.remarks = response.body.remarks;
        this.asset.updated_at = response.body.updated_at;
      });
    },

    getCompanyAssets(index) {
      this.asset = this.assets[index];
    },

    createCompanyAsset() {
      swal("Create new asset ?", {
        buttons: {
          Yes: true,
          cancel: "Cancel"
        }
      }).then(value => {
        switch (value) {
          case "Yes":
            this.$router.push({
              path: "/company_assets"
            });
            break;

          default:
            break;
        }
      });
    },
    createMOP() {
      if (this.addMode.name != "") {
        swal("Create new Mode of Payment?", {
          buttons: {
            Yes: true,
            cancel: "Cancel"
          }
        }).then(value => {
          switch (value) {
            case "Yes":
              this.$http
                .post("api/mode_of_payment", this.addMode)
                .then(response => {
                  // console.log(response.body);
                  this.loadMode();
                  swal(this.addMode.name, "has successfully added!", "success");
                })
                //   break;
                .catch(response => {
                  swal({
                    title: "Error",
                    text: response.body.error,
                    icon: "error",
                    dangerMode: true
                  }).then(value => {
                    if (value) {
                      this.$refs.name.focus();
                    }
                  });
                });

            default:
              break;
          }
        });
      } else {
        swal("Asset type name is required.", "Information", "info");
      }
    },
    getMode(index) {
      this.mode = this.modes[index];
    },
    updateMOP() {
      if (this.mode.mode != null) {
        this.$http
          .put("api/mode_of_payment/" + this.mode.id, this.mode)
          .then(response => {
            this.loadMode();
            // console.log(response.body);
            swal(this.mode.mode, "has successfully updated!", {
              icon: "success"
            });

            this.mode = {};
          })
          .catch(response => {
            swal({
              title: "Error",
              text: response.body.error,
              icon: "error",
              dangerMode: true
            }).then(value => {
              if (value) {
              }
            });
          });
      } else {
        swal("type name is required.", "Information", "info");
      }
    },
    createTerm() {
      if (this.addTerm.name != "") {
        swal("Create new Term of Payment?", {
          buttons: {
            Yes: true,
            cancel: "Cancel"
          }
        }).then(value => {
          switch (value) {
            case "Yes":
              this.$http
                .post("api/term", this.addTerm)
                .then(response => {
                  // console.log(response.body);
                  this.loadTerms();
                  swal(this.addTerm.name, "has successfully added!", "success");
                })
                //   break;
                .catch(response => {
                  swal({
                    title: "Error",
                    text: response.body.error,
                    icon: "error",
                    dangerMode: true
                  }).then(value => {
                    if (value) {
                      this.$refs.name.focus();
                    }
                  });
                });

            default:
              break;
          }
        });
      } else {
        swal("Asset type name is required.", "Information", "info");
      }
    },
    getTerm(index) {
      this.term = this.terms[index];
    },
    updateTerm() {
      if (this.term.term != null) {
        this.$http
          .put("api/term/" + this.term.id, this.term)
          .then(response => {
            // this.loadTerms();
            console.log(response.body);
            swal(this.term.term, "has successfully updated!", {
              icon: "success"
            });

            // this.term = {};
          })
          .catch(response => {
            swal({
              title: "Error",
              text: response.body.error,
              icon: "error",
              dangerMode: true
            }).then(value => {
              if (value) {
              }
            });
          });
      } else {
        swal("type name is required.", "Information", "info");
      }
    },

    searchKeyword() {
      this.$http.post("api/componentSearch", this.search).then(response => {
        this.warehouses = response.body.warehouse;
        this.categories = response.body.category;
        this.types = response.body.type;
        this.assets = response.body.asset;
        this.modes = response.body.mode;
        this.terms = response.body.term;
      });
    }
  }
};
</script>

<style>
.nav-tabs {
  border-bottom: 2px solid #2b982b !important;
}

.nav-tabs .nav-link.active,
.nav-tabs .nav-item.show .nav-link {
  background: #2b982ba4 !important;
  color: #ffffff !important;
  height: 94%;
}

.nav-tabs > li > a {
  border: none !important;
  color: #1e0b0b !important;
  background: transparent !important;
  -webkit-border-radius: 0;
  -moz-border-radius: 0;
  -ms-border-radius: 0;
  border-radius: 0;
  height: 94%;
}

.nav-tabs > li > a:hover,
.nav-tabs > li > a:active,
.nav-tabs > li > a:focus {
  background: #2b982b6e !important;
  height: 94%;
}

textarea {
  resize: none;
}

.table-wrap {
  height: 500px;
  overflow-y: auto;
  overflow-x: hidden;
  border: 1px solid #eee;
}

/* width */
::-webkit-scrollbar {
  width: 5px;
}

/* Track */
::-webkit-scrollbar-track {
  background: #f1f1f1;
}

/* Handle */
::-webkit-scrollbar-thumb {
  background: #888;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
  background: #555;
}

/* search box */
</style>
