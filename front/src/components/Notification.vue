<template>
  <div class="container-fluid">
    <!-- <pre-loader></pre-loader> -->
    <div class="row clearfix">
      <div class="col-md-6">
        <div class="card">
          <div class="header">
            <h2><i class="material-icons">notification_important</i> ALERTS</h2>
          </div>
          <div class="body">
            <div class="media" v-show="alerts.length < 1">
              <div class="media-body text-center">
                No alert notification yet.
              </div>
            </div>
            <div v-for="alert in alerts" :key="alert.id">
              <div
                class="media alert alert-danger"
                v-show="alert.totalItem < 1 && alert.status == 'no'"
              >
                <div class="media-left">
                  <i class="material-icons col-red">remove_shopping_cart</i>
                </div>
                <div class="media-body">
                  <div v-if="roles.update_item">
                    <router-link
                      tag="span"
                      :to="'/items/' + alert.item_id + '/edit'"
                      v-bind:style="'color: black; cursor:pointer;'"
                    >
                      Itemcode
                      <strong>#{{ alert.item_id }}</strong>
                      {{ alert.description }}
                      is
                      <strong class="col-red alert-link">Out of stock!</strong>
                      put quantity in it.
                    </router-link>
                  </div>
                  <div v-else>
                    <span class="col-black">
                      Itemcode
                      <strong>#{{ alert.item_id }}</strong>
                      {{ alert.description }}
                      is
                      <strong class="col-red alert-link">Out of stock!</strong>
                      put quantity in it.
                    </span>
                  </div>
                </div>
              </div>
              <div
                class="media alert alert-warning"
                v-show="alert.totalItem > 0 && alert.status == 'no'"
              >
                <div class="media-left">
                  <i class="material-icons col-orange">warning</i>
                </div>
                <div class="media-body">
                  <div v-if="roles.update_item">
                    <router-link
                      tag="span"
                      :to="'/items/' + alert.item_id + '/edit'"
                      v-bind:style="'color: black; cursor:pointer;'"
                    >
                      Itemcode
                      <strong>#{{ alert.item_id }}</strong>
                      {{ alert.description }}
                      is
                      <strong class="col-orange alert-link"
                        >running low!</strong
                      >
                      add quantity in it.
                    </router-link>
                  </div>
                  <div v-else>
                    <span class="col-black">
                      Itemcode
                      <strong>#{{ alert.item_id }}</strong>
                      {{ alert.description }}
                      is
                      <strong class="col-orange alert-link"
                        >running low!</strong
                      >
                      add quantity in it.
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <div class="header">
            <h2><i class="material-icons">question_answer</i> ACTIVITY</h2>
          </div>
          <div class="body">
            <div
              class="media"
              v-for="notification in notifications"
              :key="notification.id"
              v-show="notifications.length > 0"
            >
              <div class="media-left">
                <router-link
                  tag="a"
                  :to="'/sales_order/' + notification.id + '/edit'"
                  v-show="notification.status == 'approval'"
                >
                  <img
                    class="media-object"
                    src="http://placehold.it/32x32"
                    width="32"
                    height="32"
                  />
                </router-link>
                <router-link
                  tag="a"
                  :to="'/list/sales_order'"
                  v-show="notification.status == 'approved'"
                >
                  <img
                    class="media-object"
                    src="http://placehold.it/32x32"
                    width="32"
                    height="32"
                  />
                </router-link>
              </div>
              <div class="media-body">
                <div v-if="roles.update_sales_order">
                  <router-link
                    tag="span"
                    :to="'/sales_order/' + notification.id + '/edit'"
                    v-show="notification.status == 'approval'"
                    v-bind:style="'color: black; cursor:pointer;'"
                  >
                    <b>{{ notification.user.name }}</b> submitted
                    <b>Sales Order #{{ notification.id }}</b> for approval.
                    <br />
                    <small>{{ notification.created_at }}</small>
                  </router-link>
                </div>
                <div v-else>
                  <span
                    v-show="notification.status == 'approval'"
                    class="col-black"
                  >
                    <b>{{ notification.user.name }}</b> submitted
                    <b>Sales Order #{{ notification.id }}</b> for approval.
                    <br />
                    <small>{{ notification.created_at }}</small>
                  </span>
                </div>

                <div v-if="roles.create_delivery_receipt">
                  <router-link
                    tag="span"
                    :to="'/list/sales_order'"
                    v-show="notification.status == 'approved'"
                    v-bind:style="'color: black; cursor:pointer;'"
                  >
                    <b>Sales Order #{{ notification.id }}</b>
                    is ready for
                    <b>delivery</b>.
                    <br />
                    <small>{{ notification.created_at }}</small>
                  </router-link>
                </div>
                <div v-else>
                  <span
                    v-show="notification.status == 'approved'"
                    class="col-black"
                  >
                    <b>Sales Order #{{ notification.id }}</b>
                    is ready for
                    <b>delivery</b>.
                    <br />
                    <small>{{ notification.created_at }}</small>
                  </span>
                </div>
              </div>
            </div>
            <div class="media" v-show="notifications.length < 1">
              <div class="media-body text-center">
                No activity notification yet.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import PreLoader from "./PreLoader.vue";

export default {
  components: {
    "pre-loader": PreLoader
  },

  data() {
    return {
      notifications: [],
      alerts: [],
      roles: [],
      authenticatedUser: []
    };
  },

  created() {
    this.authenticatedUser = this.$global.getUser();
    this.getNotification();
    this.getUserRoles();
  },

  methods: {
    getNotification() {
      this.$http.post("api/notification").then(response => {
        this.notifications = response.body;
      });

      this.$http.post("api/notification/alert").then(response => {
        this.alerts = response.body;
      });
    },

    getUserRoles() {
      this.$http
        .get("api/users/" + this.authenticatedUser.id)
        .then(response => {
          this.$global.setRoles(response.body.roles);
          this.roles = this.$global.getRoles();
          $(".page-loader-wrapper").fadeOut();
        });
    }
  }
};
</script>

<style scoped>
i {
  font-size: 22px;
  vertical-align: middle;
  margin-bottom: 5px;
}

.alert {
  border-radius: 2px;
}

.alert-danger {
  border-left: 4px solid #e4a2a2;
  background-color: #ffeae8 !important;
}

.alert-warning {
  border-left: 4px solid #ffc16c;
  background-color: #ffefd9 !important;
}
</style>
