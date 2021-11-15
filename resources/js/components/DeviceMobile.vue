<template>
    <div class="container-fluid">
	    <div class="row">
	        <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{id}}</h3>
                    </div>

                    <div class="card-body table-responsive">

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
		data() {
            return {
                id: this.$route.params.id
            }
        }
    }
</script>
