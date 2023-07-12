<template>
  <div class="container-fluid">
    <div class="col-md-4 col-md-offset-4">
      <div class="card">
        <form @submit.prevent="create">
          <div class="header">
            <h2>NEW TYPE</h2>
          </div>
          <div class="body">
            <div class="row clearfix">
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>Type Name</label>
                <div class="input-group">
                  <div class="form-line">
                    <input
                      name="name"
                      type="text"
                      class="form-control"
                      v-validate="'required'"
                      v-model.trim="type.name"
                      autocomplete="off"
                    >
                  </div>
                  <small class="col-red" v-show="errors.has('name')">{{ errors.first('name') }}</small>
                </div>
              </div>
            </div>
            <div class="row clearfix">
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>Description</label>
                <div class="input-group">
                  <div class="form-line">
                    <textarea
                      name="description"
                      type="text"
                      class="form-control"
                      v-validate="'required'"
                      v-model.trim="type.description"
                      rows="3"
                    ></textarea>
                  </div>
                  <small
                    class="col-red"
                    v-show="errors.has('description')"
                  >{{ errors.first('description') }}</small>
                </div>
              </div>
            </div>

            <div class="row clearfix">
              <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <span class="text text-success"></span>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <input
                  type="submit"
                  value="CREATE"
                  class="btn btn-lg bg-black waves-effect waves-light font-bold pull-right"
                >
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import swal from "sweetalert";

export default {
  data() {
    return {
      types: [],
      type: {
        name: "",
        description: ""
      }
    };
  },

  created() {
    this.getType();
  },

  methods: {
    getType() {
      this.$http.get("api/type").then(response => {
        this.types = response.body;
      });
    },

    create() {
      this.$validator.validateAll().then(() => {
        this.$http
          .post("api/type", this.type)
          .then(response => {
            swal(this.type.name, "was successfully added!", {
              icon: "success"
            });
            this.getType();
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
