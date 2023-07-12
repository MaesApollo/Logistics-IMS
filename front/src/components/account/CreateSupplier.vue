<template>
  <div class="container-fluid">
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
            <h2>Create New Supplier</h2>
          </div>
          <div class="body">
            <div class="row clearfix">
              <div class="col-sm-12">
                <span>Name</span>
                <div class="input-group">
                  <div class="form-line">
                    <input
                      type="text"
                      ref="name"
                      name="name"
                      class="form-control"
                      v-validate="'required'"
                      v-model.trim="supplier.name"
                      autocomplete="off"
                      autofocus="on"
                    />
                  </div>
                  <small
                    class="text-danger pull-left"
                    v-show="errors.has('name')"
                    >Name is required.</small
                  >
                </div>
              </div>
            </div>

            <div class="row clearfix">
              <div class="col-sm-12">
                <span>Registered TIN</span>
                <div class="input-group">
                  <div class="form-line">
                    <input
                      type="text"
                      ref="name"
                      name="name"
                      class="form-control"
                      v-validate="'required'"
                      v-model.trim="supplier.tin"
                      autocomplete="off"
                      autofocus="on"
                    />
                  </div>
                  <small
                    class="text-danger pull-left"
                    v-show="errors.has('name')"
                    >Name is required.</small
                  >
                </div>
              </div>
            </div>

            <div class="row clearfix">
              <div class="col-sm-12">
                <span>Email</span>
                <div class="input-group">
                  <div class="form-line">
                    <input
                      type="email"
                      name="email"
                      class="form-control"
                      v-validate="{ email: true }"
                      v-model.trim="supplier.email"
                      autocomplete="off"
                    />
                  </div>
                  <small
                    class="text-danger pull-left"
                    v-show="errors.has('email')"
                    >{{ errors.first("email") }}</small
                  >
                </div>
              </div>
            </div>

            <div class="row clearfix">
              <div class="col-sm-12">
                <span>Contact</span>
                <div class="input-group">
                  <div class="form-line">
                    <input
                      type="text"
                      name="contact"
                      class="form-control"
                      v-model.trim="supplier.contact"
                      autocomplete="off"
                    />
                  </div>
                </div>
              </div>
            </div>

            <div class="row clearfix">
              <div class="col-sm-12">
                <span>Address</span>
                <div class="input-group">
                  <div class="form-line">
                    <textarea
                      type="text"
                      name="address"
                      class="form-control"
                      v-model.trim="supplier.address"
                      rows="5"
                    ></textarea>
                  </div>
                </div>
              </div>
            </div>

            <div class="row clearfix">
              <div class="col-md-12 col-sm-12">
                <div class="input-group">
                  <span>Locale</span>
                  <div class="form-line">
                    <select
                      name="locale"
                      class="form-control"
                      v-validate="'required'"
                      v-model="supplier.locale_id"
                    >
                      <option
                        v-for="locale in locales"
                        :key="locale.id"
                        v-bind:value="locale.id"
                        >{{ locale.name }}</option
                      >
                    </select>
                  </div>
                  <small
                    class="text-danger pull-left"
                    v-show="errors.has('locale')"
                    >Locale is required.</small
                  >
                </div>
              </div>
            </div>

            <div class="row clearfix">
              <div class="col-md-12">
                <button
                  class="btn btn-lg btn-info waves-effect waves-light pull-right"
                  @click="create"
                  :disabled="!roles.create_item"
                >
                  <span>Create</span>
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
export default {
  data() {
    return {
      locales: null,
      supplier: {
        name: null,
        contact: null,
        address: null,
        tin: null,
        email: null,
        locale_id: 1
      },
      roles: []
    };
  },

  created() {
    this.getLocale();
    this.roles = this.$global.getRoles();
  },

  methods: {
    getLocale() {
      this.$http.get("api/locale").then(response => {
        this.locales = response.body;
      });
    },

    create() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$http
            .post("api/supplier", this.supplier)
            .then(response => {
              swal(this.supplier.name, "was successfully created!", {
                icon: "success"
              });

              this.$http.get("api/supplier").then(response => {
                this.$global.setSupplier(response.body);

                this.$router.push({
                  path: "/supplier"
                });
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
              path: "/supplier"
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
</style>
