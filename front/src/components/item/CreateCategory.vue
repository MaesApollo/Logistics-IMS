<template>
  <div class="container-fluid">
    <div class="block-header">
      <button type="button" class="btn btn-default waves-effect" @click="exit">
        <i class="material-icons">keyboard_backspace</i>
        <span>Back</span>
      </button>
    </div>
    <div class="row clearfix">
      <div class="col-lg-4 col-md-4">
        <div class="card">
          <div class="header">
            <h2>Create New Category</h2>
          </div>
          <form @submit.prevent="create">
            <div class="body">
              <div class="row clearfix">
                <div class="col-lg-12 col-md-12">
                  <span>Category Name</span>
                  <div class="input-group">
                    <div class="form-line">
                      <input
                        ref="name"
                        name="name"
                        type="text"
                        class="form-control"
                        v-validate="'required'"
                        v-model.trim="category.name"
                        autocomplete="off"
                      />
                    </div>
                    <small
                      class="text-danger pull-left"
                      v-show="errors.has('name')"
                      >Category name is required.</small
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
      </div>
    </div>
  </div>
</template>

<script>
import swal from "sweetalert";

export default {
  data() {
    return {
      category: {
        name: ""
      }
    };
  },

  // create() {
  //   this.$refs.name.focus();
  // },

  methods: {
    // create() {
    //   this.$validator.validateAll().then(result => {
    //     if (result) {
    //       this.$http.post("api/category", this.category).then(response => {
    //         this.$global.setCategories(response.body);
    //         swal(this.category.name, "was successfully added!", "success");
    //         this.$router.push({
    //           path: "/manage/category"
    //         });
    //       });
    //     }
    //   });
    // },

    create() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$http
            .post("api/category", this.category)
            .then(response => {
              this.$http.get("api/category").then(response => {
                this.$global.setCategories(response.body);
                swal(this.category.name, "was successfully added!", "success");
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
