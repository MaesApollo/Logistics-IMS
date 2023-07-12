<template>
  <table class="table table-striped table-condensed table-hover" id="itemTable">
    <thead>
      <tr>
        <th class="font-bold" v-for="column in columns" :key="column">{{ column }}</th>
      </tr>
    </thead>
    <tbody>
      <router-link
        tag="tr"
        v-for="item in items"
        :key="item.id"
        :to="'/items/' + item.id + '/edit'"
        style="cursor: pointer;"
      >
        <td>{{ item.id }}</td>
        <td>{{ item.name }}</td>
        <td>{{ item.description }}</td>
        <td>{{ item.category.name }}</td>
        <td>{{ item.type.name }}</td>
        <td class="bg-red" v-show="item.forecast.totalItem < 1 && item.forecast.status == 'no'">
          <span>No Stock</span>
        </td>
        <td class="bg-orange" v-show="item.forecast.totalItem > 0 && item.forecast.status == 'no'">
          <span>Low Stock</span>
        </td>
        <td class="bg-green" v-show="item.forecast.totalItem > 0 && item.forecast.status == 'ok'">
          <span>High Stock</span>
        </td>
        <td>
          <span v-if="item.stocks.length < 1">0</span>
          <span v-else>{{ item.total_qty }}</span>
        </td>
      </router-link>
      <tr v-show="items.length == 0">
        <td colspan="14" class="text-center">
          <small class="col-red">
            <i>No results found.</i>
          </small>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
import swal from "sweetalert";

export default {
  props: ["items", "category", "type", "warehouses", "search"],

  data() {
    return {
      columns: [
        "Product Code#",
        "Name",
        "Description",
        "Category",
        "Type",
        "Status",
        "Total"
      ],
      roles: []
    };
  },

  created() {
    this.roles = this.$global.getRoles();
  },

  methods: {
    getItem() {
      this.$http.get("api/items").then(response => {
        this.items = response.body;
      });
    }
  }
};
</script>

<style scoped>
</style>
