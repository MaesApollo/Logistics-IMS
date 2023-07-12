<template>
  <div class="container-fluid">
    <div class="block-header">
      <div class="row clearfix">
        <div class="col-lg-10 col-md-10">
          <button
            type="button"
            class="btn btn-default waves-effect"
            @click="exit"
          >
            <i class="material-icons">keyboard_backspace</i>
            <span>Back</span>
          </button>
        </div>
      </div>
    </div>
    <div class="row clearfix">
      <div class="col-md-8">
        <div class="card">
          <div class="header">
            <h2>Accountability</h2>
          </div>
          <div class="body">
            <div class="row clearfix">
              <div class="col-sm-6">
                <br />
                <span>Asset Name</span>

                <multiselect
                  v-model="selectedItems"
                  :options="items"
                  label="description"
                  track-by="id"
                  :custom-text="getItemDesc"
                  variant="success"
                  multiple
                  tag-placeholder="Add this item"
                  open-direction="bottom"
                  placeholder="Type to search"
                  :hide-selected="true"
                  :clear-on-select="false"
                  :close-on-select="false"
                  :taggable="true"
                ></multiselect>
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

                <small class="text-danger pull-left" v-show="errors.has('name')"
                  >Area name is required.</small
                >
              </div>

              <div class="col-sm-6">
                <br />
                <span>Type</span>

                <select
                  class="form-control"
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

            <div class="row clearfix">
              <div class="col-md-6 col-md-offset-6">
                <br />
                <input
                  type="submit"
                  value="Create"
                  @click="create"
                  class="btn btn-lg btn-info waves-effect waves-light pull-right"
                  v-show="roles.create_comp"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import swal from "sweetalert";
import PreLoader from "../PreLoader.vue";

import datePicker from "vue-bootstrap-datetimepicker";
import "pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css";
import { ModelListSelect } from "vue-search-select";
import Multiselect from "vue-multiselect";

export default {
  components: {
    datePicker,
    "pre-loader": PreLoader,
    "model-list-select": ModelListSelect,
    Multiselect
  },
  data() {
    return {
      categories: [],
      roles: [],
      types: [],
      asset: {},
      items: [],
      item: "",
      options: {
        format: "YYYY-MM-DD",
        useCurrent: false
      },
      selectedItems: []
    };
  },

  mounted() {
    this.roles = this.$global.getRoles();
  },
  created() {
    this.$http.get("api/company_assets_type").then(response => {
      this.types = response.body;
    });
    this.loadItems();
  },

  methods: {
    getItemDesc(item) {
      return `${item.name} - ${item.description}`;
    },
    loadItems() {
      this.$http.get("api/items").then(response => {
        this.items = response.body;
      });
    },
    create() {
      console.log(this.selectedItems);
      // this.$validator.validateAll().then(result => {
      //   if (result) {
      //     swal("Create this Asset?", {
      //       buttons: {
      //         yes: "yes",
      //         cancel: "cancel"
      //       }
      //     }).then(value => {
      //       switch (value) {
      //         case "yes":
      //           this.$http
      //             .post("api/company_assets", this.asset)
      //             .then(response => {
      //               this.$http.get("api/company_assets").then(response => {
      //                 this.$global.setCompanyAssets(response.body);
      //               });

      //               swal("Asset Created.", {
      //                 icon: "success"
      //               });
      //             });
      //           break;
      //         default:
      //           break;
      //       }
      //     });
      //   }
      // });
    },
    exit() {
      swal("Are you sure you want to go back?", {
        icon: "warning",
        buttons: {
          exit: "Yes",
          cancel: true
        }
      }).then(value => {
        switch (value) {
          case "exit":
            this.$router.go(-1);
            break;

          default:
            break;
        }
      });
    }
  }
};
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
