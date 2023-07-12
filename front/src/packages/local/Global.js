export default function(Vue) {
  Vue.global = {
    setItems(items) {
      localStorage.setItem("_items", JSON.stringify(items));
    },

    setItemGroups(item_groups) {
      localStorage.setItem("_item_groups", JSON.stringify(item_groups));
    },

    setUsers(users) {
      localStorage.setItem("_users", JSON.stringify(users));
    },

    setClients(clients) {
      localStorage.setItem("_clients", JSON.stringify(clients));
    },

    setToClient(client) {
      localStorage.setItem("_to", JSON.stringify(client));
    },

    setOrder(orders) {
      localStorage.setItem("_orders", JSON.stringify(orders));
    },

    setPurchaseOrders(purchase_orders) {
      localStorage.setItem("_purchase_orders", JSON.stringify(purchase_orders));
    },

    setTempPurchaseOrder(purchase_order) {
      localStorage.setItem(
        "_temp_purchase_order",
        JSON.stringify(purchase_order)
      );
    },

    setCategories(categories) {
      localStorage.setItem("_categories", JSON.stringify(categories));
    },
    setWarehouses(warehouses) {
      localStorage.setItem("_warehouses", JSON.stringify(warehouses));
    },

    setTypes(types) {
      localStorage.setItem("_types", JSON.stringify(types));
    },

    setSalesOrder(sales_order) {
      localStorage.setItem("_sales_order", JSON.stringify(sales_order));
    },

    setNumberOfPending(number) {
      localStorage.setItem("_number_of_pending", JSON.stringify(number));
    },

    setNumberOfVerified(verified) {
      localStorage.setItem("_number_of_verified", JSON.stringify(verified));
    },

    setNumberOfClient(client) {
      localStorage.setItem("_number_of_client", JSON.stringify(client));
    },

    setSupplier(supplier) {
      localStorage.setItem("_supplier", JSON.stringify(supplier));
    },
    setDepartment(department) {
      localStorage.setItem("_department", JSON.stringify(department));
    },

    setUser(user) {
      localStorage.setItem("_user", JSON.stringify(user));
    },

    setRoles(roles) {
      localStorage.setItem("_roles", JSON.stringify(roles));
    },

    setAmount(amount) {
      localStorage.setItem("_amount", JSON.stringify(amount));
    },

    setSalesReturn(sales_return) {
      localStorage.setItem("_sales_return", JSON.stringify(sales_return));
    },

    setCompanyAssets(assets) {
      localStorage.setItem("_assets", JSON.stringify(assets));
    },

    setDeliveryReceipt(delivery_receipt) {
      localStorage.setItem(
        "_delivery_receipt",
        JSON.stringify(delivery_receipt)
      );
    },

    getItems() {
      return JSON.parse(localStorage.getItem("_items"));
    },

    getItemGroups() {
      return JSON.parse(localStorage.getItem("_item_groups"));
    },

    getUsers() {
      return JSON.parse(localStorage.getItem("_users"));
    },

    getToClient() {
      return JSON.parse(localStorage.getItem("_to"));
    },

    getOrder() {
      return JSON.parse(localStorage.getItem("_orders"));
    },

    getPurchaseOrders() {
      return JSON.parse(localStorage.getItem("_purchase_orders"));
    },

    getTempPurchaseOrder() {
      return JSON.parse(localStorage.getItem("_temp_purchase_order"));
    },

    getClients() {
      return JSON.parse(localStorage.getItem("_clients"));
    },

    getCategories() {
      return JSON.parse(localStorage.getItem("_categories"));
    },

    getWarehouses() {
      return JSON.parse(localStorage.getItem("_warehouses"));
    },

    getTypes() {
      return JSON.parse(localStorage.getItem("_types"));
    },

    getSalesOrder() {
      return JSON.parse(localStorage.getItem("_sales_order"));
    },

    getNumberOfPending() {
      return JSON.parse(localStorage.getItem("_number_of_pending"));
    },

    getNumberOfVerified() {
      return JSON.parse(localStorage.getItem("_number_of_verified"));
    },

    getNumberOfClient() {
      return JSON.parse(localStorage.getItem("_number_of_client"));
    },

    getSupplier() {
      return JSON.parse(localStorage.getItem("_supplier"));
    },
    getDepartment() {
      return JSON.parse(localStorage.getItem("_department"));
    },

    getUser() {
      return JSON.parse(localStorage.getItem("_user"));
    },

    getRoles() {
      return JSON.parse(localStorage.getItem("_roles"));
    },

    getAmount() {
      return JSON.parse(localStorage.getItem("_amount"));
    },

    getDeliveryReceipt() {
      return JSON.parse(localStorage.getItem("_delivery_receipt"));
    },

    getDeliveryDate() {
      return JSON.parse(localStorage.getItem("_delivery_date"));
    },
    getSalesReturn() {
      return JSON.parse(localStorage.getItem("_sales_return"));
    },
    getCompanyAssets() {
      return JSON.parse(localStorage.getItem("_assets"));
    },

    destroyGlobal() {
      localStorage.removeItem("_to");
      localStorage.removeItem("_items");
      localStorage.removeItem("_clients");
      localStorage.removeItem("_categories");
      localStorage.removeItem("_warehouses");
      localStorage.removeItem("_types");
      localStorage.removeItem("_orders");
      localStorage.removeItem("_sales_order");
      localStorage.removeItem("_number_of_pending");
      localStorage.removeItem("_number_of_verified");
      localStorage.removeItem("_number_of_client");
      localStorage.removeItem("_user");
      localStorage.removeItem("_users");
      localStorage.removeItem("_roles");
      localStorage.removeItem("_supplier");
      localStorage.removeItem("_department");
      localStorage.removeItem("_amount");
      localStorage.removeItem("_delivery_receipt");
      localStorage.removeItem("_purchase_orders");
      localStorage.removeItem("_sales_return");
      localStorage.removeItem("_assets");
    }
  };

  Object.defineProperties(Vue.prototype, {
    $global: {
      get() {
        return Vue.global;
      }
    }
  });
}
