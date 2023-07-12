<template>
	 <div class="container-fluid">
      <div class="row clearfix">
        <div class="col-lg-5 col-md-5 col-md-offset-4">
            <div class="card">
                <div class="header">
                  <h2>List of Types</h2>
                </div>
                <div class="body">
                  <ul class="list-group">
                    <li class="list-group-item" v-for="type in types" :key="type.id">
                      <div class="media">
                        <div class="media-body">
                          <h4 class="media-heading">{{ type.name }}</h4>
                            {{ type.description }}
                        </div>
                      </div>
                    </li>
                  </ul>
                </div>
            </div>
        </div>
      </div>
    </div>
</template>

<script>
import swal from 'sweetalert'

export default {
  data () {
      return {
        types: [],
      }
    },

    created () {
      this.types = this.$global.getTypes();
    },

    methods: {

    }
}
</script>