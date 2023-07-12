<template>
  <div class="container-fluid">
    <div class="qrBtn-div">
      <b-button class="quickReport-btn" @click="quickReport"
        >Quick Report</b-button
      >
    </div>

    <!-- Summary reports-->
    <div class="row clearfix" style="margin-top:30px">
      <!-- stock monitoring table -->
      <div class="col-md-12" style="height:520px">
        <div class="card card-div">
          <div class="header">
            <h2>Latest Forecast</h2>
          </div>
          <div class="table-wrap loading-div" v-if="alerts.length == 0">
            <img src="../img/bars.gif" height="50" />
            Fetching list...
          </div>
          <div class="table-wrap" v-else>
            <!-- running low forecast -->
            <div class="rlfMain-div">
              <div class="rlflabel-div">
                <label class="rlf-label"
                  ><!-- <span
                    ><i class="material-icons warning-icon">warning</i></span
                  > -->
                  &nbsp; Running Low</label
                >
                <!-- <label
                  style="display:block;background: linear-gradient(to right, green, orange); height:8px"
                ></label> -->
              </div>
              <table
                class="table table-borderless table-items"
                style="font-size: small; overflow-y: scroll"
              >
                <tbody>
                  <div v-if="runningLow.length == 0" class="empty-div">
                    <img src="../img/empty.gif" height="100" /><br />
                    No data to display.
                  </div>
                  <tr
                    v-for="alert in runningLow"
                    :key="alert.item_id"
                    v-show="alert.totalItem > 0 && alert.status == 'no'"
                  >
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
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- out of stocks forecast -->

            <div class="osfMain-div">
              <div class="osflabel-div">
                <label class="osf-label" style="font-size: small"
                  >Out of Stock</label
                >
                <!-- <label
                  style="display:block;background: linear-gradient(to right, orange, #cc2b00); height:8px"
                ></label> -->
              </div>
              <table
                class="table table-borderless"
                style="font-size: small; overflow-y: scroll"
              >
                <tbody>
                  <div v-if="outOfStock.length == 0" class="empty-div">
                    <img src="../img/empty.gif" height="100" /><br />
                    No data to display.
                  </div>
                  <tr
                    v-for="alert in outOfStock"
                    :key="alert.item_id"
                    v-show="alert.totalItem < 1 && alert.status == 'no'"
                  >
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
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <br />

    <!-- <div class="footer">
      <div style="margin-left:40%">
        <img src="./../img/inet.gif" style="width:12%" />
        <img
          src="./../img/giga.gif"
          style="width:25%;margin-left:25px;margin-right:20px"
        />
        <img src="./../img/solutions.gif" style="width:12%" />
      </div>
    </div> -->
    <br />
    <br />
  </div>
</template>

<script>
import VueRangedatePicker from "vue-rangedate-picker";
import { ModelListSelect } from "vue-search-select";

export default {
  components: {
    "rangedate-picker": VueRangedatePicker,
    "model-list-select": ModelListSelect
  },

  data() {
    return {
      reports: [],
      clients: [],
      filterBy: "",
      clientSelected: null,
      sumDateSelected: {},
      value: "",
      users: [],
      items: [],
      successful_order: [],
      sales_returns: [],

      items: [],
      alerts: [],
      notifications: [],
      roles: [],
      user: [],
      runningLow: [],
      outOfStock: []
    };
  },
  created() {
    this.user = this.$global.getUser();
    console.log(this.$global.getUser());
    this.load();
    // this.roles = this.$global.getRoles();
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
    linkGen(pageNum) {
      return pageNum === 1 ? "?" : `?page=${pageNum}`;
    },

    quickReport() {
      swal("You will be redirected to Quick Report.Continue?", {
        buttons: {
          Yes: true,
          cancel: "Cancel"
        }
      }).then(value => {
        switch (value) {
          case "Yes":
            this.$router.push({
              path: "/qreport"
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
* {
  box-sizing: border-box;
}

.input-date {
  display: block;
  border: 1px solid rgb(1, 235, 71) !important;
  padding: 5px;
  font-size: 15px !important;
  width: 100% !important;
  cursor: pointer;
}

select,
textarea {
  width: 100%;
  padding: 5px;
  border: 1px solid rgb(1, 235, 71);
  border-radius: 4px;
  resize: vertical;
}

.labelNew {
  padding: 12px 12px 12px 0;
  display: inline-block;
}

input[type="submit"] {
  background-color: #4caf50;
  color: white;
  padding: 12px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  float: right;
}

input[type="submit"]:hover {
  background-color: #45a049;
}

.container {
  border-radius: 5px;
  background-color: #f2f2f2;
  padding: 20px;
}

.col-25 {
  float: left;
  width: 25%;
  margin-top: 6px;
}

.col-75 {
  float: left;
  width: 75%;
  margin-top: 6px;
}

/* Responsive layout - when the screen is less than 600px wide, make the two columns stack on top of each other instead of next to each other */
@media screen and (max-width: 600px) {
  .col-25,
  .col-75,
  input[type="submit"] {
    width: 100%;
    margin-top: 0;
  }
}

.button {
  background-color: #4caf50; /* Green */
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  cursor: pointer;
  -webkit-transition-duration: 0.4s; /* Safari */
  transition-duration: 0.4s;
}
.qrBtn-div {
  width: 100%;
  text-align: right !important;
}
.quickReport-btn {
  transition: all 0.5s ease;
  width: 10%;
  background: #5a5a5a;
}
.quickReport-btn:hover {
  background: #363636;
}
.card-div {
  height: 100%;
  /* overflow-y: scroll; */
}
.rlfMain-div {
  padding: 0;
  width: 49.9%;
  float: left;
  height: 90%;
  position: absolute;
  overflow-y: auto;
}
.rlflabel-div {
  background: #f8f8f8;
}
.rlf-label {
  color: #d46900;
  padding-left: 12px;
  letter-spacing: 0.05em;
  display: table-cell;
  vertical-align: middle;
  height: 35px;
}
.osfMain-div {
  padding: 0;
  right: 0;
  width: 49.9%;
  float: right;
  height: 85%;
  position: absolute;
  overflow-y: auto;
}
.osflabel-div {
  background: #f8f8f8;
}
.osf-label {
  color: #cc2b00;
  padding-left: 12px;
  letter-spacing: 0.05em;
  display: table-cell;
  vertical-align: middle;
  height: 35px;
}
.warning-icon {
  background: gold;
}
.forecast-cell {
  padding: 7px !important;
  padding-left: 10px !important;
  display: table-cell;
  vertical-align: middle;
}
.empty-div {
  display: flex;
  justify-content: center;
  align-items: center;
  font-weight: bold;
  letter-spacing: 0.05em;
  flex-direction: column;
  /* height: 100%; */
  position: absolute;
  height: 90%;
  width: 100%;
  opacity: 0.1;
}
.loading-div {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  height: 100%;
}
/* .footer {
  position: fixed;
  left: 0;
  bottom: 0;
  width: 100%;
  height: 73px;
  background-color: #fff;
  color: white;
  opacity: 0.5;
} */
</style>
