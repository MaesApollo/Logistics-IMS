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
            <h2>Create New Group</h2>
          </div>
          <!-- <form @submit.prevent="create"> -->
          <div class="body">
            <div class="row clearfix">
              <div class="col-sm-6" style="margin-top:-20px">
                <br />
                <span>Product Group Name</span>
                <div class="input-group">
                  <div class="form-line">
                    <input
                      ref="group_name"
                      name="group_name"
                      type="text"
                      class="form-control"
                      v-validate="'required'"
                      v-model.trim="item_selected.group_name"
                      autocomplete="off"
                      autofocus="on"
                    />
                  </div>
                  <small
                    class="text-danger pull-left"
                    v-show="errors.has('group_name')"
                    >Name is required.</small
                  >
                </div>
              </div>
              <div class="col-sm-6">
                <button
                  class="btn btn-default waves-effect"
                  data-toggle="modal"
                  data-target="#itemModal"
                  style="float:right;"
                >
                  <i class="material-icons">note_add</i>
                  <span>Add Items</span>
                </button>
              </div>
            </div>

            <div class="row clearfix">
              <div class="col-md-12">
                <div class="table-wrap">
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Item Code</th>
                          <th>Name</th>
                          <th>Quantity</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td colspan="9" class="text-center"></td>
                        </tr>
                      </tbody>
                      <tbody>
                        <tr
                          v-for="order in item_selected.orders"
                          :key="order.id"
                        >
                          <td>{{ order.id }}</td>
                          <td>{{ order.name }}</td>
                          <td>
                            <input
                              type="text"
                              v-model="order.qty"
                              style="width:30px"
                            />
                          </td>
                          <td>
                            <a
                              href="javascript:void(0);"
                              title="remove"
                              @click="removefromSelected(order.id)"
                            >
                              <i
                                class="material-icons text-danger"
                                style="font-size: 16px !important"
                                >delete</i
                              >
                            </a>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <div class="row clearfix" style="float:right">
              <div class="col-md-6 col-md-offset-6">
                <!-- <input
                    type="submit"
                    value="Create"
                    class="btn btn-lg btn-info waves-effect waves-light pull-right"
                  /> -->

                <button
                  type="button"
                  class="btn btn-lg btn-info waves-effect waves-light pull-right"
                  @click="create"
                >
                  Create
                </button>
              </div>
            </div>
          </div>
          <!-- </form> -->
        </div>
      </div>
    </div>

    <!-- modal for items -->
    <div
      class="modal fade"
      id="itemModal"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
      data-keyboard="false"
    >
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
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
            <div id="snackbar">Item Added</div>
            <div class="row clearfix">
              <div class="col-md-4">
                <div class="form-group">
                  <div class="form-line">
                    <span>Search</span>
                    <input
                      id="search"
                      type="text"
                      class="form-control"
                      v-model="search.item"
                      autocomplete="off"
                      @keyup="searchItem"
                    />
                  </div>
                </div>
              </div>
              <div class="col-md-8">
                <br />
                <button class="btn btn-sm bg-black waves-effect waves-light">
                  Search
                </button>
                <button class="btn btn-sm btn-success waves-effect">
                  Reset
                </button>
              </div>
            </div>
            <div class="row clearfix">
              <div class="col-md-12">
                <div class="table-wrap">
                  <table
                    class="table table-stripped table-condensed table-hover"
                    id="tblSearchItem"
                  >
                    <thead>
                      <tr>
                        <th>Add</th>
                        <th>Category</th>
                        <th>Description</th>
                        <!-- <th>Code</th> -->
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="item in items"
                        :key="item.id"
                        style="cursor: pointer"
                      >
                        <td>
                          <button
                            class="btn btn-lg btn-success waves-effect"
                            @click="addtoGroup(item)"
                          >
                            ADD
                          </button>
                        </td>
                        <td>{{ item.category.name }}</td>
                        <td>
                          {{ item.name }} -
                          {{ item.description }}
                        </td>
                        <!-- <td>{{ item.id }}</td> -->

                        <td></td>
                      </tr>
                      <tr v-if="items.length == 0">
                        <td colspan="3" class="text-center">
                          No results found.
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- end  of modal for items -->
  </div>
</template>

<script>
import swal from "sweetalert";

export default {
  data() {
    return {
      groups: [],
      group: {},
      items: [],
      imageFile: null,
      item: {
        name: "",
        items: []
      },
      search: {
        item: "",
        client: "",
        serial: ""
      },

      item_selected: {
        group_name: "",
        orders: []
      }
    };
  },

  created() {
    this.loadGroups();
    this.loadItems();
  },

  methods: {
    loadItems() {
      this.$http.get("api/items").then(response => {
        this.items = response.body;
      });
    },
    loadGroups() {
      this.$http.post("api/items/showItemGroup").then(response => {
        this.groups = response.body;
        console.log(this.groups);
      });
    },
    getGroup(index) {
      console.log(index);
      this.item_selected.orders = this.groups[index];
      console.log(this.item_selected.orders);
    },
    create() {
      console.log(this.item_selected.group_name);
      console.log(this.item_selected.orders);

      this.$validator.validateAll().then(result => {
        if (result) {
          swal("Create this item group?", {
            buttons: {
              agree: "Yes",
              cancel: true
            }
          }).then(value => {
            switch (value) {
              case "agree":
                this.$http
                  .post("api/items/addGroup", this.item_selected)
                  .then(response => {
                    swal("A new  item group was successfully added!", {
                      icon: "success"
                    });
                    // this.$http.get("api/items").then(response => {
                    //   this.$global.setItems(response.body);
                    this.$router.push({
                      path: "/inventory"
                    });
                    // });
                  });
                break;
              default:
                break;
            }
          });
        }
      });
    },

    addtoGroup(item) {
      // this.item_selected.orders.push({
      //   id: item.id,
      //   name: item.name,
      //   qty: ""
      // });
      var exists = false;

      for (var i = 0; i < this.item_selected.length; i++) {
        if (this.item_selected.orders[i].id == item.id) {
          exists = true;
        }
      }

      if (!exists) {
        this.item_selected.orders.push({
          id: item.id,
          name: item.name,
          qty: ""
        });
      }
      var x = document.getElementById("snackbar");
      x.className = "show";
      setTimeout(function() {
        x.className = x.className.replace("show", "");
      }, 600);

      this.search.item = "";
      this.searchItem();
    },
    searchItem() {
      var filter, table, tr, targetTableColCount;
      filter = this.search.item.toUpperCase();
      table = document.getElementById("tblSearchItem");
      tr = table.getElementsByTagName("tr");
      for (var i = 0; i < tr.length; i++) {
        var rowData = "";

        if (i == 0) {
          targetTableColCount = table.rows.item(i).cells.length;
          continue; //do not execute further code for header row.
        }
        for (var colIndex = 0; colIndex < targetTableColCount; colIndex++) {
          //console.log(table.rows.item(i).cells.item(colIndex).textContent);
          rowData += table.rows.item(i).cells.item(colIndex).textContent;
        }

        if (rowData.toUpperCase().indexOf(filter) == -1) {
          table.rows.item(i).style.display = "none";
        } else {
          table.rows.item(i).style.display = "table-row";
        }
      }
    },
    removefromSelected(id) {
      for (var index = 0; index < this.item_selected.orders.length; index++) {
        if (this.item_selected.orders[index].id == id) {
          this.item_selected.orders.splice(index, 1);
        }
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
            this.$router.push({
              path: "/inventory"
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

<style>
#snackbar {
  visibility: hidden;
  min-width: 250px;
  margin-left: -125px;
  background-color: #333;
  color: #fff;
  text-align: center;
  border-radius: 2px;
  padding: 16px;
  position: fixed;
  z-index: 1;
  left: 40%;
  top: 100px;
  font-size: 17px;
}

#snackbar.show {
  visibility: visible;
  -webkit-animation: fadein 1s, fadeout 1s 1s;
  animation: fadein 1s, fadeout 1s 1s;
}
</style>
