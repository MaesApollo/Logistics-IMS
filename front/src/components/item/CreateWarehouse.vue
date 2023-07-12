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
            <h2>Create New Warehouse</h2>
          </div>
          <form @submit.prevent="create">
            <div class="body">
              <div class="row clearfix">
                <div class="col-lg-12">
                  <br />
                  <label>Warehouse Name</label>
                  <div class="input-group">
                    <div class="form-line">
                      <input
                        ref="name"
                        name="name"
                        type="text"
                        class="form-control"
                        v-validate="'required'"
                        v-model.trim="warehouse.name"
                        autocomplete="off"
                      />
                    </div>
                    <small
                      class="text-danger pull-left"
                      v-show="errors.has('name')"
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
                        name="address"
                        type="text"
                        class="form-control"
                        v-model.trim="warehouse.address"
                        rows="5"
                      ></textarea>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-md-6 col-md-offset-6">
                  <input
                    type="submit"
                    class="btn btn-lg btn-info waves-effect waves-light pull-right"
                    value="Create"
                  />
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import swal from "sweetalert";

export default {
  data() {
    return {
      warehouse: {
        name: "",
        address: ""
      }
    };
  },

  methods: {
    create() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$http
            .post("api/warehouse", this.warehouse)
            .then(response => {
              this.$http.get("api/warehouse").then(response => {
                this.$global.setWarehouses(response.body);
                swal(this.warehouse.name, "was successfully added!", "success");
                this.$router.go(-1);
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

<style scoped>
textarea {
  resize: none;
}
</style>
