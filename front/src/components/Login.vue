<template>
  <div class="container-fluid">
    <div class="container-div">
      <div class="formMiddle-div">
        <div class="loginImg-div">
          <img
            src="../assets/lock.gif"
            alt="LOGISTICS IMS"
            class="loginTop-img"
          />
        </div>
        <form @submit.prevent="login" class="form-signin">
          <div class="input-group field-div">
            <span class="input-group-addon span-append" id="basic-addon1">
              <i class="material-icons input-icon">email</i>
            </span>
            <input
              type="text"
              ref="email"
              name="email"
              class="form-control fields"
              placeholder="Email"
              v-model.trim="email"
              v-validate="'required'"
              autocomplete="off"
              aria-describedby="basic-addon1"
              autofocus
              @keyup.enter="login"
            />
          </div>
          <small class="text-danger error-span" v-show="errors.has('email')">
            {{ errors.first("email") }}
          </small>
          <div v-if="!passwordHidden" class="field-div">
            <div class="input-group">
              <span class="input-group-addon span-append">
                <i class="material-icons input-icon" style="color: #fff"
                  >vpn_key
                </i>
              </span>
              <input
                type="text"
                name="password"
                class="form-control fields"
                placeholder="Password"
                aria-label="Password"
                aria-describedby="basic-addon2"
                v-model="password"
                v-validate="'required'"
                @keyup.enter="login"
              />
              <span @click="hidePassword" class="input-group-addon span-click">
                <i class="material-icons">visibility_off</i>
              </span>
            </div>
            <small
              class="text-danger error-span error-span2"
              v-show="errors.has('password')"
            >
              {{ errors.first("password") }}
            </small>
          </div>
          <div v-if="passwordHidden" class="field-div">
            <div class="input-group">
              <span class="input-group-addon span-append">
                <i class="material-icons input-icon">vpn_key</i>
              </span>
              <input
                type="password"
                name="password"
                class="form-control fields"
                placeholder="Password"
                aria-label="Password"
                aria-describedby="basic-addon2"
                v-model="password"
                v-validate="'required'"
                @keyup.enter="login"
              />
              <span @click="showPassword" class="input-group-addon span-click">
                <i class="material-icons">visibility</i>
              </span>
            </div>
            <small
              class="text-danger error-span error-span2"
              v-show="errors.has('password')"
            >
              {{ errors.first("password") }}
            </small>
          </div>
          <b-button class="login-btn" size="sm" type="submit" @click="login">
            L O G I N
          </b-button>
          <small class="text-danger center-align" v-show="login_failed"
            >Incorrect credentials.</small
          >
        </form>
        <div class="loginImg-div">
          <img
            src="../assets/dctech logo.gif"
            alt="STOCK INVENTORY MANAGEMENT SYSTEM"
            class="loginBottom-img"
          />
        </div>
      </div>
      <div class="footer">
        <!-- <span class="footerLeft-span">
          LOGISTICS IMS
        </span>
        <span class="footerRight-span">
          ©2021 VERSION 1.0
        </span> -->
        <span class="center-span">
          LOGISTICS IMS ©2021 BETA VERSION
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import swal from "sweetalert";

export default {
  name: "Login",
  data() {
    return {
      email: "",
      password: "",
      remember: "",
      isAuth: null,
      login_failed: false,
      passwordHidden: {
        default: false,
        type: Boolean
      }
    };
  },

  created() {
    this.isAuth = this.$auth.isAuthenticated();
  },

  methods: {
    login() {
      var subEmail = this.email;

      if (!subEmail.includes("@dctechmicro.com"))
        subEmail += "@dctechmicro.com";

      var data = {
        client_id: 2,
        client_secret: "7vhYZQttQlhd6sAq7Wg1beLIJN31Jyg7PGDyKoQx",
        grant_type: "password",
        username: subEmail,
        password: this.password
      };

      this.$validator.validateAll().then(result => {
        if (result) {
          this.$http
            .post("oauth/token", data)
            .then(response => {
              console.log(response.body);
              this.$auth.setToken(
                response.body.access_token,
                response.body.expires_in + Date.now()
              );
              window.location.href = "/";
            })
            .catch(response => {
              console.log(response);
              this.$refs.email.focus();
              this.login_failed = true;
              swal("Error", "Incorrect credentials!", "error");
            });
        }
      });
    },
    checkDB() {
      this.$http.get("api/users").then(response => {
        console.log(response.body);
      });
    },
    hidePassword() {
      this.passwordHidden = true;
    },
    showPassword() {
      this.passwordHidden = false;
    }
  }
};
</script>

<style>
/* Extra small devices (phones, 600px and down) */
@media only screen and (max-width: 600px) {
  .formMiddle-div {
    width: 96% !important;
    padding-left: 10px !important;
    padding-right: 10px !important;
  }
}
/* Small devices (portrait tablets and large phones, 600px and up) */
@media only screen and (min-width: 600px) {
  .formMiddle-div {
    width: 96% !important;
    padding-left: 10px !important;
    padding-right: 10px !important;
  }
}
/* Medium devices (landscape tablets, 768px and up) */
@media only screen and (min-width: 768px) {
  .formMiddle-div {
    width: 96% !important;
    padding-left: 10px !important;
    padding-right: 10px !important;
  }
}
/* Large devices (laptops/desktops, 992px and up) */
@media only screen and (min-width: 992px) {
  .formMiddle-di {
    width: 96% !important;
    padding-left: 10px !important;
    padding-right: 10px !important;
  }
}
/* Extra large devices (large laptops and desktops, 1200px and up) */
@media only screen and (min-width: 1200px) {
  .formMiddle-div {
    width: 30% !important;
    padding: 50px !important;
  }
}
@media only screen and (min-width: 1500px) {
  .formMiddle-div {
    width: 25% !important;
    padding: 50px !important;
  }
}
.container-fluid {
  height: 100%;
  width: 100%;
  align-items: center;
  justify-content: center;
  background: #242424;
}
.container-div {
  display: flex;
  justify-content: center;
  flex-direction: column;
  width: 100%;
  height: 100%;
  align-items: center;
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  /* background-image: linear-gradient(#b7cdd3, #faf7f4); */
  background-image: linear-gradient(-20deg, #378b64 0%, #e9e9e9 100%);
}
.formMiddle-div {
  width: 30%;
  background: #ffffff66;
  border-radius: 10px;
  margin-top: 4px;
  margin-bottom: 4px;
  padding: 50px;
}
.version-div {
  justify-content: flex-end;
  align-self: flex-end;
  margin-bottom: 5px;
}
.fields {
  background-color: #ffffff;
  font-size: small;
  color: #1b1b1b;
  border-radius: 0 0 0 0;
  border: 0;
  padding-right: 35px;
  padding-left: 12px !important;
}
.error-span,
.error-span2 {
  display: block;
  /* text-align: right; */
  color: red;
  margin-bottom: 12px;
  margin-top: -10px;
}
.error-span2 {
  margin-top: -19px;
}
.center-align {
  font-weight: bold;
  display: block;
  text-align: center;
  margin-top: 10px;
  margin-bottom: -18px;
}
.pass-eye {
  border-radius: 0 0 0 0;
  border-width: 0 0 2px;
  border-color: #ffffff;
  padding-top: 0;
  background-color: #ffffff;
  color: #1b1b1b;
}
.fields:focus,
.pass-eye:focus,
.pass-eye:hover {
  outline: none !important;
  box-shadow: none;
  background-color: #ffffff;
  color: #1b1b1b;
}
.login-btn {
  transition: all 0.5s ease;
  color: #ffffff;
  font-family: "TW Cen MT";
  font-weight: bold;
  background-color: #aa938f;
  border: 0;
  padding: 7px;
  border-radius: 0 0 0 0;
  width: 100%;
  margin-top: 10px;
}
.login-btn:hover,
.login-btn:focus {
  background-color: #dabea6;
  padding: 7px;
  border: 0;
  /* color: #19b45f; */
  color: #ffffff;
}
.span-click {
  cursor: pointer;
  border-radius: 0;
  background: #f5f5f5 !important;
  padding-left: 8px !important;
  padding-right: 8px !important;
}
.span-append {
  border-radius: 0;
  background: #edd8c8 !important;
  padding-right: 0;
  padding-left: 0;
}
.input-icon {
  color: #ffffff !important;
  margin-left: 8px;
  margin-right: 8px;
}
.field-div {
  margin-bottom: 12px;
}
.loginTop-img {
  height: 60px;
  margin-bottom: 40px;
}
.loginBottom-img {
  height: 50px;
  margin-top: 40px;
}
.loginImg-div {
  width: 100%;
  text-align: center;
}
.footer {
  padding: 14px;
  background: rgba(0, 0, 0, 0.1);
  color: #ffffff;
  font-weight: bold;
  font-family: "TW Cen MT";
  letter-spacing: 0.15em;
  width: 100%;
  position: absolute;
  bottom: 0;
  display: flex;
}
.footerLeft-span {
  flex: 1;
  text-align: left;
}
.footerRight-span {
  flex: 1;
  text-align: right;
}
.center-span {
  flex: 1;
  text-align: center;
}
</style>
