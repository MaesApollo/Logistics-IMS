<template>
  <div>
    <nav class="navbar navbar-light">
      <div class="container-fluid">
        <!-- <pre-loader></pre-loader> -->
        <div style="background:black">
          <a href="javascript:void(0);" class="bars"></a>
        </div>
        <div class="logo">
          <router-link to="/">
            <a class="navbar-brand" href="javascript:void(0);">
              <img src="../img/ims.gif" width="150" alt="User" />
            </a>
          </router-link>
        </div>
        <div style="margin-right:12px">
          <ul class="notification-drop">
            <li class="item">
              <i class="material-icons col-black" title="Running Low"
                >battery_alert</i
              >
              <span class="btn__badge pulse-button" title="Running Low">{{
                runningLow.length
              }}</span>
              <ul style="overflow-y:scroll;max-height:300px">
                <li><td>Running Low</td></li>
                <li v-for="alert in runningLow" :key="alert.item_id">
                  <td class="forecast-cell">
                    <a
                      :href="'/items/' + alert.item_id + '/edit'"
                      target="_blank"
                    >
                      {{ alert.item_id }}
                    </a>
                  </td>
                  <td class="forecast-cell">
                    {{ alert.description }}
                  </td>
                </li>
              </ul>
            </li>
          </ul>
        </div>
        <div style="margin-right:12px">
          <ul class="notification-drop">
            <li class="item">
              <i class="material-icons col-black" title="Out of Stocks"
                >remove_shopping_cart</i
              >
              <span class="btn__badge pulse-button " title="Out of Stocks">{{
                outOfStock.length
              }}</span>
              <ul style="overflow-y:scroll;max-height:300px">
                <li><td>Out of Stocks</td></li>
                <li v-for="alert in outOfStock" :key="alert.item_id">
                  <td class="forecast-cell">
                    <a
                      :href="'/items/' + alert.item_id + '/edit'"
                      target="_blank"
                    >
                      {{ alert.item_id }}
                    </a>
                  </td>
                  <td class="forecast-cell">
                    {{ alert.description }}
                  </td>
                </li>
              </ul>
            </li>
          </ul>
        </div>
        <div style="margin-right:12px">
          <button
            @click="logout"
            title="Logout"
            class="logout-btn btn waves-effect"
          >
            <i class="fas fa-sign-out-alt align-icon"></i>

            <!-- <span><b>LOGOUT</b></span> -->
          </button>
        </div>
      </div>
    </nav>
    <div class="footer">
      <span class="center-span">
        LOGISTICS IMS &copy;2021
      </span>
    </div>
  </div>
</template>

<script>
import PreLoader from "./PreLoader.vue";

var moment = require("moment");
moment().format();

function showNotification(
  colorName,
  text,
  placementFrom,
  placementAlign,
  animateEnter,
  animateExit
) {
  var allowDismiss = false;

  $.notify(
    {
      message: text
    },
    {
      type: colorName,
      allow_dismiss: allowDismiss,
      newest_on_top: true,
      timer: 300,
      placement: {
        from: placementFrom,
        align: placementAlign
      },
      animate: {
        enter: animateEnter,
        exit: animateExit
      },
      template:
        '<div data-notify="container" class="bootstrap-notify-container alert alert-dismissible {0} ' +
        (allowDismiss ? "p-r-35" : "") +
        '" role="alert">' +
        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
        '<span data-notify="icon"></span> ' +
        '<span data-notify="title">{1}</span> ' +
        '<span data-notify="message">{2}</span>' +
        '<div class="progress" data-notify="progressbar">' +
        '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
        "</div>" +
        '<a href="{3}" target="{4}" data-notify="url"></a>' +
        "</div>"
    }
  );
}

export default {
  components: {
    "pre-loader": PreLoader
  },

  data() {
    return {
      authenticatedUser: [],
      roles: [],
      loading_count: 0,
      loading_max: 1,
      notifications: [],
      alerts: [],
      runningLow: [],
      outOfStock: []
    };
  },

  created() {
    this.authenticatedUser = this.$global.getUser();
    this.roles = this.$global.getRoles();
    console.log("NAVBAR VUE");
    this.load();
  },

  mounted() {},

  methods: {
    load() {
      this.$http.post("api/notification/alert").then(response => {
        console.log(response.body);
        this.alerts = response.body.alerts;
        this.runningLow = response.body.runningLow;
        this.outOfStock = response.body.outOfStock;
      });
    },
    logout() {
      this.$auth.destroyToken();
      this.$global.destroyGlobal();
      window.location.href = "/login";
    },

    setAuthenticatedUser() {
      this.$http.get("api/user").then(response => {
        this.$global.setUser(response.body);
        this.authenticatedUser = this.$global.getUser();
        this.getUserRoles();
      });
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

$(document).ready(function() {
  var down = false;

  $("#bell").click(function(e) {
    var color = $(this).text();
    if (down) {
      $("#box").css("height", "0px");
      $("#box").css("opacity", "0");
      down = false;
    } else {
      $("#box").css("height", "auto");
      $("#box").css("opacity", "1");
      down = true;
    }
  });
});

$(document).ready(function() {
  $(".notification-drop .item").on("click", function() {
    $(this)
      .find("ul")
      .toggle();
  });
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Manrope:wght@200&display=swap");

* {
  padding: 0px;
  margin: 0px;
}
.navbar {
  /* background-color: #b7cdd3; */
  background-color: #6fbe99;
  z-index: 22;
}
.icon {
  cursor: pointer;
  margin-right: 50px;
  line-height: 60px;
}

.icon span {
  background: #f00;
  padding: 7px;
  border-radius: 50%;
  color: #fff;
  vertical-align: top;
  margin-left: -25px;
}

.icon img {
  display: inline-block;
  width: 26px;
  margin-top: 4px;
}

.icon:hover {
  opacity: 0.7;
}

.logo {
  flex: 1;
  margin-left: 50px;
  height: 50px;
  padding-top: 11px;
}

.footer {
  padding: 14px;
  background: #6fbe99;
  color: #575757;
  font-weight: bold;
  font-family: "TW Cen MT";
  letter-spacing: 0.15em;
  width: 100%;
  position: fixed;
  bottom: 0;
  display: flex;
  z-index: 20;
}
.center-span {
  flex: 1;
  text-align: right;
}
.logout-btn {
  transition: all 0.5s ease;
  margin-right: 20px;
  box-shadow: 3px 3px 3px 3px #000000;
  border: 0;
  letter-spacing: 0.1em;
  color: #0f0f0f;
  background: #5aa582;
  /* background-image: linear-gradient(to right, #abc3ca, #ffffff8c, #abc3ca); */
  height: 35px;
  padding-left: 11px;
  padding-right: 8px;
}
.logout-btn:hover {
  color: #000000;
  background: #7e9da7;
  /* background-image: linear-gradient(to right, #9fbac2, #ffffff99, #9fbac2); */
}
.align-icon {
  margin-top: -3px;
}

/* ALERT NOTIF  */
ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.notification-drop {
  font-family: "Ubuntu", sans-serif;
  color: #444;
}
.notification-drop .item {
  padding: 10px;
  font-size: 18px;
  position: relative;
  border-bottom: 1px solid #ddd;
}
.notification-drop .item:hover {
  cursor: pointer;
}
.notification-drop .item i {
  margin-left: 10px;
}
.notification-drop .item ul {
  display: none;
  position: absolute;
  top: 100%;
  background: #fff;
  left: -200px;
  right: 0;
  z-index: 1;
  border-top: 1px solid #ddd;
}
.notification-drop .item ul li {
  font-size: 16px;
  padding: 15px 0 15px 25px;
}
.notification-drop .item ul li:hover {
  background: #ddd;
  color: rgba(0, 0, 0, 0.8);
}

@media screen and (min-width: 500px) {
  .notification-drop {
    display: flex;
    justify-content: flex-end;
  }
  .notification-drop .item {
    border: none;
  }
}

.notification-bell {
  font-size: 20px;
}

.btn__badge {
  background: #ff5d5d;
  color: white;
  font-size: 10px;
  position: absolute;
  top: 0;
  right: 0px;
  padding: 3px 10px;
  border-radius: 50%;
}

.pulse-button {
  box-shadow: 0 0 0 0 rgba(255, 0, 0, 0.5);
  -webkit-animation: pulse 3.5s infinite;
}

.pulse-button:hover {
  -webkit-animation: none;
}

@-webkit-keyframes pulse {
  0% {
    -moz-transform: scale(0.9);
    -ms-transform: scale(0.9);
    -webkit-transform: scale(0.9);
    transform: scale(0.9);
  }
  70% {
    -moz-transform: scale(1);
    -ms-transform: scale(1);
    -webkit-transform: scale(1);
    transform: scale(1);
    box-shadow: 0 0 0 50px rgba(255, 0, 0, 0);
  }
  100% {
    -moz-transform: scale(0.9);
    -ms-transform: scale(0.9);
    -webkit-transform: scale(0.9);
    transform: scale(0.9);
    box-shadow: 0 0 0 0 rgba(255, 0, 0, 0);
  }
}

.notification-text {
  font-size: 14px;
  font-weight: bold;
}

.notification-text span {
  float: right;
}

.forecast-cell {
  padding: 1px !important;
  padding-left: 10px !important;
  display: table-cell;
  vertical-align: middle;
  font-size: 10px;
  color: #000000 !important;
}

/* width */
::-webkit-scrollbar {
  width: 10px;
}

/* Track */
::-webkit-scrollbar-track {
  background: #f1f1f1;
}

/* Handle */
::-webkit-scrollbar-thumb {
  background: #888;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
