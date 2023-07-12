import Vue from "vue";
import VueRouter from "vue-router";

import Login from "./components/Login.vue";
import Dashboard from "./components/Dashboard.vue";
import Order from "./components/Order.vue";
import Inventory from "./components/Inventory.vue";
import Accounts from "./components/Accounts.vue";
import Settings from "./components/Settings.vue";
import SalesReturn from "./components/order/SalesReturn.vue";
import Notification from "./components/Notification.vue";
import ItemOrdered from "./components/account/ItemOrdered.vue";

import ManageCategory from "./components/item/ManageCategory.vue";
import ManageType from "./components/item/ManageType.vue";
import ManageWarehouse from "./components/item/ManageWarehouse.vue";
import ManageSalesOrder from "./components/order/ManageSalesOrder.vue";
import ManageSalesReturn from "./components/order/ManageSalesReturn.vue";
import ManagePurchaseOrder from "./components/order/ManagePurchaseOrder.vue";
import ManageClient from "./components/account/ManageClient.vue";
import ManageSupplier from "./components/account/ManageSupplier.vue";
import ManageDepartment from "./components/account/ManageDepartment.vue";
import ManageDeliveryReceipt from "./components/order/ManageDeliveryReceipt.vue";
import ManageCompanyAssets from "./components/company_assets/ManageCompanyAssets.vue";
import ViewSalesReturn from "./components/order/ViewSalesReturn.vue";
import ManageReceives from "./components/order/ManageReceives.vue";
import ManageItemReceipt from "./components/order/ManageItemReceipt.vue";
import ManageSupplierBills from "./components/order/ManageSupplierBills.vue";
import ManageDirectReceives from "./components/order/ManageDirectReceives.vue";
import ManageAccountability from "./components/order/ManageAccountability.vue";

import CreateCompanyAssets from "./components/company_assets/CreateCompanyAssets.vue";
import CreateItem from "./components/item/CreateItem.vue";
import CreateGroupItem from "./components/item/CreateGroupItem.vue";
import CreateCategory from "./components/item/CreateCategory.vue";
import CreateType from "./components/item/CreateType.vue";
import CreateWarehouse from "./components/item/CreateWarehouse.vue";
import CreateAccount from "./components/account/CreateAccounts.vue";
import CreateClient from "./components/account/CreateClient.vue";
import CreateSupplier from "./components/account/CreateSupplier.vue";
import CreateDeliveryReceipt from "./components/order/CreateDeliveryReceipt.vue";
import EditPurchaseOrder from "./components/order/EditPurchaseOrder.vue";
import CreatePurchaseOrder from "./components/order/CreatePurchaseOrder.vue";
import CreateReceiveItems from "./components/order/CreateReceiveItems.vue";
import CreateReceivingReport from "./components/order/CreateReceivingReport.vue";
import CreateMaterialRequest from "./components/order/CreateMaterialRequest.vue";

import ModifyCompanyAssets from "./components/company_assets/ModifyCompanyAssets.vue";
import EditItem from "./components/item/EditItem.vue";
import EditSalesOrder from "./components/order/EditSalesOrder.vue";

import Report from "./components/Report.vue";
import Duplicate from "./components/Duplicate.vue";
import Component from "./components/Components.vue";
// import Test from "./components/Maintenance.vue";
import Test from "./components/Test.vue";

import QuickReport from "./components/QuickReport.vue";

// import Error404 from "./components/error/404.vue";

Vue.use(VueRouter);

const router = new VueRouter({
    routes: [{
            path: "/",
            component: QuickReport,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/login",
            component: Login,
            meta: {
                forVisitors: true
            }
        },
        {
            path: "/home",
            component: Dashboard,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/inventory",
            component: Inventory,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/create/item",
            component: CreateItem,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/create/itemGroup",
            component: CreateGroupItem,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/manage/category",
            component: ManageCategory,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/manage/type",
            component: ManageType,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/manage/warehouse",
            component: ManageWarehouse,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/create/category",
            component: CreateCategory,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/create/type",
            component: CreateType,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/create/warehouse",
            component: CreateWarehouse,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/items/:item/edit",
            component: EditItem,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/sales_order/:order",
            component: Order,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/material_request",
            component: CreateMaterialRequest,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/purchase_orders",
            component: ManagePurchaseOrder,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/supplier_bills/:purchase_order",
            component: ManageSupplierBills,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/manage_receivingReports",
            component: ManageReceives,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/manage_itemReceipts",
            component: ManageItemReceipt,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/new_purchase_order",
            component: CreatePurchaseOrder,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/purchase_order/:purchase_order",
            component: EditPurchaseOrder,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/receiving",
            component: CreateReceivingReport,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/receiving/:receive",
            component: CreateReceivingReport,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/accounts",
            component: Accounts,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/client",
            component: ManageClient,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/supplier",
            component: ManageSupplier,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/department",
            component: ManageDepartment,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/settings",
            component: Settings,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/create/account",
            component: CreateAccount,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/create/client",
            component: CreateClient,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/create/supplier",
            component: CreateSupplier,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/list/sales_order",
            component: ManageSalesOrder,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/list/accountability",
            component: ManageAccountability,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/sales_return",
            component: SalesReturn,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/list/sales_return",
            component: ManageSalesReturn,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/sales_return/:sales_return_id",
            component: ViewSalesReturn,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/delivery_receipt/:order",
            component: CreateDeliveryReceipt,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/sales_order/:order/edit",
            component: EditSalesOrder,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/notifications",
            component: Notification,
            meta: {
                forAuth: true
            }
        },
        // {
        //   path: "/error404",
        //   component: Error404
        // },
        {
            path: "/test",
            component: Test
        },
        {
            path: "/account/:client/ordered",
            component: ItemOrdered,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/delivery_receipts",
            component: ManageDeliveryReceipt,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/receive_items",
            component: CreateReceiveItems,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/direct_receives",
            component: ManageDirectReceives,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/list/company_assets",
            component: ManageCompanyAssets,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/company_assets",
            component: CreateCompanyAssets,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/company_assets/:asset_id/edit",
            component: ModifyCompanyAssets,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/report",
            component: Report,
            meta: {
                forAuth: true
            }
        },
        {
            path: "/duplicate",
            component: Duplicate,
            meta: {
                forAuth: true
            }
        },

        {
            path: "/component",
            component: Component,
            meta: {
                forAuth: true
            }
        },

        {
            path: "/qreport",
            component: QuickReport,
            meta: {
                forAuth: true
            }
        }
    ],

    // linkActiveClass: "active",

    mode: "history"
});

export default router;