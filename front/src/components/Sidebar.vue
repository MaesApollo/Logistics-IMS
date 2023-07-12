<template>
  <section>
    <!-- Left Sidebar -->
    <aside id="leftsidebar" class="sidebar mt-n5">
      <!-- User Info -->
      <div class="user-info">
        <div class="image">
          <!-- <img src="../img/female.gif" width="48" height="48" alt="User" /> -->
          <!-- <img src="../img/asdf.png" width="48" height="48" alt="User" /> -->
          <img src="../img/user.gif" width="48" height="48" alt="User" />
        </div>
        <div class="info-container">
          <div
            class="name"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
          >
            {{ authenticatedUser.name }}
          </div>
          <div class="email">{{ authenticatedUser.email }}</div>

          <div class="btn-group user-helper-dropdown">
            <router-link to="/settings">
              <a href="javascript:void(0);" title="Profile Settings">
                <i class="material-icons setting-icon">settings</i>
                <!-- <img src="../img/install.gif" width="28" height="28" /> -->
              </a>
            </router-link>
          </div>
        </div>
      </div>
      <!-- #User Info -->
      <!-- Menu -->
      <div class="menu">
        <ul class="list">
          <li class="header">MAIN NAVIGATION</li>
          <li v-show="roles.view_purchase_order">
            <a href="javascript:void(0);" class="menu-toggle">
              <i class="material-icons">shopping_cart</i>
              <span>Purchasing</span>
            </a>
            <ul class="ml-menu">
              <router-link tag="li" to="/purchase_orders">
                <a href="javascript:void(0);"
                  >Purchase Orders
                  <span class="badge" id="badge-warning">{{
                    pendingPurchase
                  }}</span></a
                >
              </router-link>
              <router-link tag="li" to="/direct_receives">
                <a href="javascript:void(0);">Direct Receive Items</a>
              </router-link>
              <!-- <router-link tag="li" to="/manage_receivingReports">
                <a href="javascript:void(0);">Receiving Reports</a>
              </router-link>
              <router-link tag="li" to="/manage_itemReceipts">
                <a href="javascript:void(0);">Item Receipts</a>
              </router-link> -->
            </ul>
          </li>
          <li>
            <a href="javascript:void(0);" class="menu-toggle">
              <i class="material-icons">description</i>
              <span>Sale Ordering</span>
            </a>
            <ul class="ml-menu">
              <router-link tag="li" to="/list/sales_order">
                <a href="javascript:void(0);"
                  >Material Requests
                  <span class="badge" id="badge-warning">{{
                    pendingMRequest
                  }}</span></a
                >
              </router-link>
              <router-link tag="li" to="/delivery_receipts">
                <a href="javascript:void(0);">Delivery Receipts</a>
              </router-link>
              <router-link tag="li" to="/list/sales_return">
                <a href="javascript:void(0);"
                  >Transmittal Receipts
                  <span class="badge" id="badge-warning">{{
                    pendingReturn
                  }}</span></a
                >
              </router-link>
            </ul>
          </li>

          <li>
            <a href="javascript:void(0);" class="menu-toggle">
              <i class="material-icons">archive</i>
              <span>Inventory</span>
            </a>
            <ul class="ml-menu">
              <router-link tag="li" to="/inventory">
                <a href="javascript:void(0);">Products</a>
              </router-link>
              <router-link tag="li" to="/component">
                <a href="javascript:void(0);">Components</a>
              </router-link>
              <router-link tag="li" to="/list/accountability">
                <a href="javascript:void(0);">Accountability</a>
              </router-link>
            </ul>
          </li>
          <li>
            <a href="javascript:void(0);" class="menu-toggle">
              <i class="material-icons">people</i>
              <span>Accounts</span>
            </a>
            <ul class="ml-menu">
              <router-link
                tag="li"
                to="/accounts"
                v-show="roles.update_account || roles.create_account"
              >
                <a href="javascript:void(0);">Accounts</a>
              </router-link>
              <router-link tag="li" to="/client">
                <a href="javascript:void(0);">Clients</a>
              </router-link>
              <router-link tag="li" to="/supplier">
                <a href="javascript:void(0);">Suppliers</a>
              </router-link>
              <router-link tag="li" to="/department" v-show="roles.admin">
                <a href="javascript:void(0);">Departments</a>
              </router-link>
            </ul>
          </li>
          <li role="seperator" class="divider"></li>
          <!-- <li>
            <a href="javascript:void(0);">
              <i class="material-icons">help</i>
              <span>Help</span>
            </a>
          </li> -->
          <!-- <li>
            <a href="javascript:void(0);" @click="logout">
              <i class="material-icons">logout</i>
              <span>Log out</span>
            </a>
          </li> -->
        </ul>
      </div>
      <!-- #Menu -->
      <!-- Footer -->
      <div class="legal">
        <!-- <div class="copyright">LOGISTICS IMS &copy;2021</div> -->
        <div
          class="version"
          id="footerVersion"
          data-toggle="modal"
          data-target="#versionModal"
        >
          <b>Version:</b> BETA
        </div>
      </div>
      <!-- #Footer -->
    </aside>
    <!-- #END# Left Sidebar -->

    <div
      class="modal fade"
      id="versionModal"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
      data-keyboard="false"
    >
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Version History</h4>
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
            <div class="row clearfix">
              <div class="col-lg-12 col-md-12">
                <div class="block-header">
                  <h5>Version 1.0.1</h5>
                  <b>
                    <p>Released: May 03, 2019</p>
                  </b>
                  <p>Launching of the website/system</p>
                </div>
              </div>
            </div>
            <hr />
            <div class="row clearfix">
              <div class="col-lg-12 col-md-12">
                <div class="block-header">
                  <h5>Version 1.0.2</h5>
                  <b>
                    <p>Released: May 24, 2019</p>
                  </b>
                  <b>
                    <p>Updates:</p>
                  </b>
                  <b>
                    <p class="twoSpace-span">Sales Return:</p>
                  </b>
                  <p class="fiveSpace-span">
                    - add manage sales return in this page user can view the
                    list of the items that had returned.
                  </p>
                  <p class="fiveSpace-span">
                    - Create Sales return are now not only one item can be
                    return.
                  </p>
                  <p class="fiveSpace-span">
                    - Create sales return are now more detailed.
                  </p>
                  <b>
                    <p class="twoSpace-span">Material Request:</p>
                  </b>
                  <p class="fiveSpace-span">
                    - Are now required to select client to send for approval.
                  </p>
                  <p class="fiveSpace-span">
                    - Fix the search client at create material request.
                  </p>
                  <p class="fiveSpace-span">
                    - Fix the search item at create material request.
                  </p>
                  <b>
                    <p class="twoSpace-span">Delivery Receipts:</p>
                  </b>
                  <p class="fiveSpace-span">
                    - Add search for any keyword of the delivery receipts.
                  </p>
                  <p class="fiveSpace-span">
                    - Add print preview and select column to print .
                  </p>
                  <p class="fiveSpace-span">
                    - Add export to excel to all the data in the table.
                  </p>
                  <b>
                    <p class="twoSpace-span">Products:</p>
                  </b>
                  <p class="fiveSpace-span">
                    - Fix the search item more efficient
                  </p>
                  <p class="fiveSpace-span">
                    - Add print preview and select column to print .
                  </p>
                  <p class="fiveSpace-span">
                    - Add export to excel to all the data in the table.
                  </p>
                  <b>
                    <p class="twoSpace-span">Category:</p>
                  </b>
                  <p class="fiveSpace-span">
                    - Change the Category to Category & type
                  </p>
                  <p class="fiveSpace-span">
                    - Add manage type for the company asset .
                  </p>
                  <p class="fiveSpace-span">
                    - Add modify and delete for the company asset type.
                  </p>
                  <b>
                    <p class="twoSpace-span">Inventory Tab:</p>
                  </b>
                  <p class="fiveSpace-span">- Add Company Assets</p>
                  <p class="fiveSpace-span">
                    - Add manage company asset in this page user can view and
                    search all the company assets.
                  </p>
                  <p class="fiveSpace-span">- Add create company asset</p>
                  <p class="fiveSpace-span">- Add update company asset</p>
                  <p class="fiveSpace-span">- Add delete company asset</p>
                  <b>
                    <p class="twoSpace-span">Accounts:</p>
                  </b>
                  <p class="fiveSpace-span">
                    - Add new Role namely Create Company Asset, Update and
                    Delete Company Asset
                  </p>
                  <p class="fiveSpace-span">
                    - Add manage type for the company asset .
                  </p>
                  <p class="fiveSpace-span">
                    - Add modify and delete for the company asset type.
                  </p>
                </div>
              </div>
            </div>
            <hr />
            <div class="row clearfix">
              <div class="col-lg-12 col-md-12">
                <div class="block-header">
                  <h5>Version 1.0.3</h5>
                  <b>
                    <p>Released: May 28, 2019</p>
                  </b>
                  <b>
                    <p>Updates:</p>
                  </b>
                  <b>
                    <p class="twoSpace-span">Material Request:</p>
                  </b>
                  <p class="fiveSpace-span">
                    - Add item in create material request are now more
                    efficient. (big impact for helpdesk)
                  </p>
                  <b>
                    <p class="twoSpace-span">Delivery Receipts:</p>
                  </b>
                  <p class="fiveSpace-span">- Print Preview add Check By.</p>
                  <b>
                    <p class="twoSpace-span">Clients:</p>
                  </b>
                  <p class="fiveSpace-span">- Add client type.</p>
                  <b>
                    <p class="twoSpace-span">Others:</p>
                  </b>
                  <p class="fiveSpace-span">
                    - minimize the loading of the system/website.
                  </p>
                  <p class="fiveSpace-span">- Add version history</p>
                  <p class="fiveSpace-span">- Fix bugs.</p>
                </div>
              </div>
            </div>
            <hr />
            <div class="row clearfix">
              <div class="col-lg-12 col-md-12">
                <div class="block-header">
                  <h5>Beta Version</h5>
                  <b>
                    <p>Released: May 14, 2021</p>
                  </b>
                  <b>
                    <p>Updates:</p>
                  </b>
                  <b>
                    <p class="twoSpace-span">Dashboard</p>
                  </b>
                  <p class="fiveSpace-span">
                    - Displays forecast of items that are low on stock and out
                    of stock.
                  </p>
                  <p class="fiveSpace-span">
                    - Quick Report: Can generate summary report filtered by
                    material request, delivery receipts, items and clients.
                  </p>
                  <b class="twoSpace-span">Purchasing </b>
                  <p class="fiveSpace-span">
                    - Can generate report by supplier.
                  </p>
                  <p class="fiveSpace-span">
                    - Enabled import of serials in direct receive items.
                  </p>
                  <b class="twoSpace-span">Material Request </b>
                  <p class="fiveSpace-span">
                    - Added Item Group button which enables users to choose a
                    group depends on the items needed for S.O, typically used
                    for material request which contains redundant items.
                  </p>
                  <b class="twoSpace-span">Delivery Receipts </b>
                  <p class="fiveSpace-span">-</p>
                  <p class="fiveSpace-span">- Add version history</p>
                  <p class="fiveSpace-span">- Fix bugs.</p>
                </div>
              </div>
            </div>
          </div>
          <hr />

          <div class="modal-footer">
            <p class="text-secondary pull-left">
              Programmers/Developers: Niel Berongoy, Peter Bismonte & Mice
              Dianne Morta.
            </p>
            <button type="button" class="btn btn-default" data-dismiss="modal">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
<script>
export default {
  components: {},

  data() {
    return {
      authenticatedUser: [],
      roles: [],
      pendingMRequest: 0,
      pendingPurchase: 0,
      pendingReturn: 0
    };
  },

  created() {
    this.setAuthenticatedUser();
    // console.log(this.$global.getUser());
    console.log("SIDEBAR VUE");
    this.setCount();
    // this.authenticatedUser = this.$global.getUser();
    // this.roles = this.$global.getRoles();
    // this.authenticatedUser = this.$global.getUser();
  },

  mounted() {
    this.$root.$on("Sidebar", () => {
      //console.log("CALL1");
      this.setCount();
    });
  },

  methods: {
    setCount() {
      this.$http.post("api/sales_order/pending/number").then(response => {
        this.pendingMRequest = response.body;
      });
      this.$http.post("api/purchase_order/pending/number").then(response => {
        this.pendingPurchase = response.body;

        // console.log(this.pendingPurchase);
      });
      this.$http.post("api/sales_return/pending/number").then(response => {
        this.pendingReturn = response.body;

        // console.log(this.pendingPurchase);
      });
    },
    logout() {
      this.$auth.destroyToken();
      this.$global.destroyGlobal();
      window.location.href = "/login";
    },

    settings() {
      window.location.href = "/settings";
    },

    setAuthenticatedUser() {
      this.$http.get("api/user").then(response => {
        console.log(response.body);
        this.$global.setUser(response.body);
        this.authenticatedUser = this.$global.getUser();
        this.getUserRoles();
      });
    },
    versionDetails() {},
    temporaryFunc() {
      // this.$http.get("api/stocks").then(response => {
      //   console.log(response.body);
      // });
    },
    getUserRoles() {
      this.$http
        .get("api/users/" + this.authenticatedUser.id)
        .then(response => {
          this.$global.setRoles(response.body.roles);
          this.roles = this.$global.getRoles();
        });
    }
  }
};
</script>
<style>
#footerVersion {
  cursor: pointer;
}

.sidebar {
  -moz-transition: all 0.5s;
  -o-transition: all 0.5s;
  -webkit-transition: all 0.5s;
  transition: all 0.5s;
  font-family: "Roboto", sans-serif;
  background: #fffefe;
  width: 280px !important;
  overflow: hidden;
  display: inline-block;
  height: calc(100vh - 50px);
  position: fixed;
  top: 80px;
  left: 0;
  -webkit-box-shadow: 2px 2px 5px rgb(0 0 0 / 10%);
  -moz-box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
  -ms-box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
  box-shadow: 2px 2px 5px rgb(0 0 0 / 10%);
  z-index: 21 !important;
}
.twoSpace-span {
  margin-left: 10px !important;
}
.fiveSpace-span {
  margin-left: 24px !important;
}

.badge {
  font-size: 8px;
  color: #fffefe;
}
#badge-warning {
  background: #e2ac08;
  color: #fffefe;
}
</style>
