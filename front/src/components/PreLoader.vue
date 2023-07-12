<template>
  <div class="page-loader-wrapper">
    <div class="loader">
      <img src="./../img/loader1.gif" class="loader-img" alt="" />
      <p>Loading...</p>
    </div>
  </div>
</template>

<script>
export default {
  created() {}
};
</script>

<style>
.loader {
  transition: all 0.5s ease;
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
  background-image: linear-gradient(-20deg, #378b64 0%, #e9e9e9 100%);
}
</style>
