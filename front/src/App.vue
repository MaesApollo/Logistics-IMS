<template>
  <div class="theme-black" id="app">
    <my-login v-if="!isAuth"></my-login>
    <!-- <my-maintenance v-if="!isAuth"></my-maintenance> -->
    <div v-if="isAuth">
      <transition name="fade" v-if="!isloaded">
        <pre-loader></pre-loader>
      </transition>
      <!-- <div v-if="pageloaded == 9"> -->
      <my-navbar></my-navbar>
      <my-sidebar></my-sidebar>

      <section class="content" v-if="isloaded">
        <router-view></router-view>
      </section>
      <!-- </div> -->
    </div>
    <!-- <div class="busyPageLoader" v-if="pageBusy">
      <img src="./img/load.gif" style="width:10%" class="imgLoader" />
      <p style="color:black">Loading...</p>
    </div> -->
  </div>
</template>

<script>
import Login from "./components/Login.vue";
import Navbar from "./components/Navbar.vue";
import Sidebar from "./components/Sidebar.vue";
import Maintenance from "./components/Maintenance.vue";
import PreLoader from "./components/PreLoader.vue";

export default {
  components: {
    "my-login": Login,
    "my-navbar": Navbar,
    "my-sidebar": Sidebar,
    "my-maintenance": Maintenance,
    "pre-loader": PreLoader
  },

  data() {
    return {
      isAuth: null,
      pageBusy: false,
      isloaded: false,
      pageloaded: 0
    };
  },

  created() {
    console.log("APP VUE");
    this.isAuth = this.$auth.isAuthenticated();
    this.load();
  },
  mounted() {
    this.$root.$on("pageLoading", () => {
      console.log("pageLoading");
      this.pageBusy = true;
      this.isloaded = true;
      // this.isAuth = null;
    });
    this.$root.$on("pageLoaded", () => {
      // console.log("pageLoaded");
      this.pageBusy = false;
    });
  },
  methods: {
    load() {
      if (this.isAuth) {
        this.$http.get("api/user").then(response => {
          // console.log(response.body);
          this.$global.setUser(response.body);
          this.getUserRoles(response.body.id);
        });
      }
    },
    getUserRoles(user_id) {
      this.$http.get("api/users/" + user_id).then(response => {
        this.$global.setRoles(response.body.roles);
        this.isloaded = true;
        this.boot();
      });
    },
    boot() {
      this.$http.get("api/users").then(response => {
        this.$global.setUsers(response.body);
        console.log("1");
      });

      this.$http.get("api/supplier").then(response => {
        this.$global.setSupplier(response.body);
        console.log("2");
      });

      this.$http.get("api/department").then(response => {
        this.$global.setDepartment(response.body);
        console.log("3");
      });

      this.$http.get("api/category").then(response => {
        this.$global.setCategories(response.body);
        console.log("5");
      });

      this.$http.get("api/warehouse").then(response => {
        this.$global.setWarehouses(response.body);
        console.log("6");
      });

      this.$http.get("api/type").then(response => {
        this.$global.setTypes(response.body);
        console.log("7");
      });

      this.$http.get("api/company_assets").then(response => {
        this.$global.setCompanyAssets(response.body);
        console.log("8");
      });
    }
  }
};
</script>

<style>
@import url(./plugins/bootstrap/css/bootstrap.css);
@import url(./plugins/node-waves/waves.css);
@import url(./plugins/animate-css/animate.css);
@import url(./plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css);
@import url(./css/style.css);
@import url(./css/themes/all-themes.css);

.fade-enter-active,
.fade-leave-active {
  transition: opacity 1s;
}
.fade-enter,
.fade-leave-to {
  opacity: 0;
}
.busyPageLoader {
  background-color: rgba(192, 192, 192, 0.3);
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 5000;
}
.imgLoader {
  margin: 0;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}
.setting-icon {
  color: #aa938f;
}
.first-shade {
  background-color: #b7cdd3;
}
.second-shade {
  background-color: #d7e3e5;
}
.third-shade {
  background-color: #aa938f;
}
.fourth-shade {
  background-color: #dabea6;
}
.fifth-shade {
  background-color: #edd8c8;
}
.sixth-shade {
  background-color: #eeeae7;
}
/* to set up */
.create-btn,
.submit-btn,
.update-btn,
.delete-btn,
.list-btn,
.toggle-btn,
.sync-btn {
  color: #ffffff !important;
  transition: 0.5s;
  background: linear-gradient(transparent, #00000041) top/100% 800%;
}
.create-btn:hover,
.submit-btn:hover,
.update-btn:hover,
.delete-btn:hover,
.list-btn:hover,
.toggle-btn:hover,
.sync-btn:hover {
  background-position: bottom;
}
.create-btn:focus,
.submit-btn:focus,
.update-btn:focus,
.delete-btn:focus,
.list-btn:focus,
.toggle-btn:focus,
.sync-btn:focus {
  box-shadow: 1px 2px 4px #c5c5c5;
  color: #ffffff;
}
.create-btn {
  background-color: #e2cc00 !important;
}
.submit-btn {
  background-color: #0ab66e !important;
}
.update-btn {
  background-color: #0097bd !important;
}
.delete-btn {
  background-color: #d44444 !important;
}
.list-btn {
  background-color: #5a5a5a !important;
}
.toggle-btn {
  background-color: #5a5a5a !important;
}
.sync-btn {
  background-color: #141414 !important;
  margin-top: 15px;
  margin-bottom: 15px;
}
</style>
