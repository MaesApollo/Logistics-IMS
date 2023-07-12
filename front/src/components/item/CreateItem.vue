<template>
  <div class="container-fluid">
    <div class="block-header">
      <button type="button" class="btn btn-default waves-effect" @click="exit">
        <i class="material-icons">keyboard_backspace</i>
        <span>Back</span>
      </button>
    </div>
    <div class="row clearfix">
      <div class="col-md-6">
        <div class="card">
          <div class="header">
            <h2>Creating New Product</h2>
          </div>
          <form @submit.prevent="create">
            <div class="body">
              <div class="row clearfix">
                <div class="col-sm-12">
                  <br />
                  <span>Product Name</span>
                  <div class="input-group">
                    <div class="form-line">
                      <input
                        ref="name"
                        name="name"
                        type="text"
                        class="form-control"
                        v-validate="'required'"
                        v-model.trim="item.name"
                        autocomplete="off"
                        autofocus="on"
                      />
                    </div>
                    <small
                      class="text-danger pull-left"
                      v-show="errors.has('name')"
                      >Item name is required.</small
                    >
                  </div>
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-sm-12">
                  <span>Description</span>
                  <div class="input-group">
                    <div class="form-line">
                      <input
                        name="description"
                        type="text"
                        class="form-control"
                        v-validate="'required'"
                        v-model.trim="item.description"
                        autocomplete="off"
                      />
                    </div>
                    <small
                      class="text-danger pull-left"
                      v-show="errors.has('description')"
                      >Item description is required.</small
                    >
                  </div>
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-sm-6">
                  <div class="input-group">
                    <span>Category</span>
                    <div class="form-line">
                      <select
                        name="category"
                        class="form-control"
                        v-validate="'required'"
                        v-model="item.category_id"
                      >
                        <option
                          v-for="category in categories"
                          :key="category.id"
                          v-bind:value="category.id"
                          >{{ category.name }}</option
                        >
                      </select>
                    </div>
                    <small
                      class="text-danger pull-left"
                      v-show="errors.has('category')"
                      >Category is required.</small
                    >
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="input-group">
                    <span>Type</span>
                    <div class="form-line">
                      <select
                        name="type"
                        class="form-control"
                        v-validate="'required'"
                        v-model="item.type_id"
                      >
                        <option
                          v-for="type in types"
                          :key="type.id"
                          v-bind:value="type.id"
                          >{{ type.name }}</option
                        >
                      </select>
                    </div>
                    <small
                      class="text-danger pull-left"
                      v-show="errors.has('type')"
                      >Type is required.</small
                    >
                  </div>
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-sm-12">
                  <span>Attach Image</span>
                  <div class="input-group">
                    <input
                      class="btn btn-lg btn-default waves-effect"
                      type="file"
                      accept="image/*"
                      @change="imageChange($event)"
                    />
                  </div>
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-md-6 col-md-offset-6">
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
      categories: [],
      types: [],
      imageFile: null,
      item: {
        name: "",
        description: "",
        type_id: "",
        category_id: "",
        supplier_id: "",
        image: ""
      }
    };
  },

  created() {
    this.categories = this.$global.getCategories();
    this.types = this.$global.getTypes();
    this.user = this.$global.getUser();
    this.types[0].name = this.types[0].name + "(modem, switch, printer)";
    this.types[1].name = this.types[1].name + "(paper, cable, accessories)";
    //console.log(this.types);
  },

  methods: {
    create() {
      this.$validator.validateAll().then(result => {
        if (result) {
          $(".page-loader-wrapper").fadeIn();
          this.$http
            .post("api/items", this.item)
            .then(response => {
              swal(this.item.name, "was successfully added!", {
                icon: "success"
              });

              // this.$global.setItems(response.body);

              this.$router.push({
                path: "/inventory"
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
    imageChange(e) {
      if (e.target.files.length > 0) {
        var fileReader = new FileReader();
        fileReader.readAsDataURL(e.target.files[0]);

        fileReader.onload = e => {
          this.item.image = e.target.result;
        };
      }
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
