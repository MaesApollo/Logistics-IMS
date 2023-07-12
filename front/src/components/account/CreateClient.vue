<template>
  <div class="container-fluid">
    <!-- <pre-loader></pre-loader> -->
    <div class="block-header">
      <button type="button" class="btn btn-default waves-effect" @click="exit">
        <i class="material-icons">keyboard_backspace</i>
        <span>Back</span>
      </button>
    </div>
    <div class="row clearfix">
      <div class="col-lg-6 col-md-6">
        <div class="card">
          <div class="header">
            <h2>Create New Client</h2>
          </div>
          <div class="body">
            <div class="row clearfix">
              <div class="col-md-12">
                <br />
                <span>Name</span>
                <div class="input-group">
                  <div class="form-line">
                    <input
                      type="text"
                      ref="name"
                      name="name"
                      class="form-control"
                      v-validate="'required'"
                      v-model.trim="client.name"
                      autocomplete="off"
                      autofocus="on"
                    />
                  </div>
                  <small
                    class="text-danger pull-left"
                    v-show="errors.has('name')"
                    >{{ errors.first("name") }}</small
                  >
                </div>
              </div>
            </div>

            <div class="row clearfix">
              <div class="col-md-12">
                <span>Contact Person</span>
                <div class="input-group">
                  <div class="form-line">
                    <input
                      type="text"
                      name="contact"
                      class="form-control"
                      v-model.trim="client.contact_person"
                      autocomplete="off"
                    />
                  </div>
                </div>
              </div>
            </div>

            <div class="row clearfix">
              <div class="col-md-12">
                <span>Contact Number</span>
                <div class="input-group">
                  <div class="form-line">
                    <input
                      type="text"
                      name="contact"
                      class="form-control"
                      v-model.trim="client.contact"
                      autocomplete="off"
                    />
                  </div>
                </div>
              </div>
            </div>

            <div class="row clearfix">
              <div class="col-md-12">
                <span>Email</span>
                <div class="input-group">
                  <div class="form-line">
                    <input
                      type="text"
                      name="contact"
                      class="form-control"
                      v-model.trim="client.email_add"
                      autocomplete="off"
                    />
                  </div>
                </div>
              </div>
            </div>

            <div class="row clearfix">
              <div class="col-md-12">
                <span>Location</span>
                <div class="input-group">
                  <div class="form-line">
                    <textarea
                      type="text"
                      name="location"
                      class="form-control"
                      v-model.trim="client.location"
                      rows="5"
                    ></textarea>
                  </div>
                </div>
              </div>
            </div>

            <div class="row clearfix">
              <div class="col-md-12">
                <span>Class</span>
                <div class="input-group">
                  <div class="form-line">
                    <model-list-select
                      class="search-list"
                      :list="client_class"
                      option-value="name"
                      option-text="name"
                      v-model="client.class"
                      v-validate="'required'"
                      placeholder="Please select class .."
                    >
                    </model-list-select>
                  </div>
                  <small
                    class="text-danger pull-left"
                    v-show="errors.has('class')"
                    >Class is required.</small
                  >
                </div>
              </div>
            </div>

            <div class="row clearfix">
              <div class="col-md-6 col-md-offset-6">
                <button
                  class="btn btn-lg btn-info waves-effect waves-light pull-right"
                  @click="create"
                  :disabled="!roles.create_client"
                >
                  Create
                </button>
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
import { ModelListSelect } from "vue-search-select";

export default {
  components: {
    "model-list-select": ModelListSelect
  },
  data() {
    return {
      client: {
        region_id: 10,
        name: "",
        owner_name: "",
        class: "",
        location: "",
        contact_person: "",
        business_type: "",
        contact: "",
        email_add: ""
      },
      roles: [],
      client_class: [
        {
          name: "INET CLIENTS"
        },
        {
          name: "SOLUTIONS CLIENTS"
        },
        {
          name: "DCTECH"
        }
      ]
    };
  },

  created() {
    this.roles = this.$global.getRoles();
  },

  methods: {
    create() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$http
            .post("api/client", this.client)
            .then(response => {
              console.log(response.body);
              swal(this.client.name, "was successfully created!", {
                icon: "success"
              });
              this.$router.push({
                path: "/client"
              });
            })
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
        }
      });
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
            this.$router.push({
              path: "/client"
            });
            break;

          default:
            break;
        }
      });
    }
  }
};
</script>

<style scoped>
textarea {
  resize: none;
}
.search-list {
  background: none;
  border: none !important;
  border-bottom: 1px solid black !important;
  border-radius: 0 0 0 0 !important;
  box-shadow: none !important;
  width: 70%;
}
</style>
