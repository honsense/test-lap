<template>
    <div>
        <md-dialog :md-active="showReqform" @md-opened="multiSelection=[]">
            <md-toolbar>
                <md-dialog-title>Request Info</md-dialog-title>
                <span style="position:absolute; right:15px">
                    <md-button v-if="multiSelection.length==0" class="md-fab md-dense md-primary" @click="newObservation">
                        <md-icon>add</md-icon>
                    </md-button>
                    <md-button v-else class="md-fab md-dense md-primary" @click="approveObs">
                        <md-icon>thumb_up</md-icon>
                    </md-button>
                </span>
            </md-toolbar>
            <div>
                <md-progress-bar md-mode="indeterminate" v-show="progress"></md-progress-bar>
            </div>
            <md-dialog-content>
            <md-tabs md-dynamic-height>
                <md-tab md-label="Request">
                <md-field>
                    <label>Reference</label>
                    <md-input v-model="form.reference"></md-input>
                </md-field>
                <md-field>
                    <label>Method</label>
                    <md-input v-model="form.method"></md-input>
                </md-field>
                <!-- <md-field>
                    <label>Requester</label>
                    <md-input :value="selecteditem.REQUESTER" v-model="form.requester"></md-input>
                </md-field> -->
                <md-field>
                    <label>Comments</label>
                    <md-input v-model="form.comments"></md-input>
                </md-field>
                </md-tab>
                <md-tab md-label="Observations">
                    <md-table :value="filteredObs" md-card @md-selected="onSelect">
                        <!-- removed from above element! md-fixed-header -->
                        <!--<md-table-toolbar>
                        <h1 class="md-title"></h1>
                        </md-table-toolbar>-->
                        <md-table-empty-state md-label="No observations found" :md-description="`No observations found.`">
                            <md-button class="md-primary md-raised" @click="newObservation">Create New Observation</md-button>
                        </md-table-empty-state>

                        <md-table-row slot="md-table-row" slot-scope="{ item }" md-selectable="multiple">
                            <!-- <md-table-cell md-label="ID" md-sort-by="id" md-numeric>{{ item.Id }}</md-table-cell> -->
                            <md-table-cell md-label="Reference">{{ item.REFERENCE }}</md-table-cell>
                            <md-table-cell md-label="Observation">{{ item.OBSERVATION }}</md-table-cell>
                            <md-table-cell md-label="Action">{{ item.ACTIONS }}</md-table-cell>
                            <md-table-cell md-label="Response">{{ item.RESPONSE }}</md-table-cell>
                            <md-table-cell md-label="Reviewer">{{ item.REVIEWER }}</md-table-cell>
                            <md-table-cell md-label="" class="update">
                                <md-icon v-if="item.APPROVED==1" class="md-primary">thumb_up</md-icon>
                                <md-button v-else class="md-primary" @click="updateObs(item)">Update</md-button>
                            </md-table-cell>
                        </md-table-row>
                    </md-table>
                </md-tab>
            </md-tabs>
            </md-dialog-content>
            <md-dialog-actions>
                <span style="position:absolute; left:15px">
                    <md-button v-if="selecteditem.APPROVED==0" class="md-primary md-raised" @click="approveReq" :disabled="obsApprovalCheck">Approve</md-button>
                    <md-icon v-else class="md-dense md-primary">thumb_up</md-icon>
                </span>
                <md-button class="md-primary" @click="closeForm">Close</md-button>
                <md-button class="md-primary" @click="postData">Save</md-button>
            </md-dialog-actions>
        </md-dialog>
    </div>
</template>

<script>
export default {
    name: 'reqview',
    data() {
        return{
            progress:false,
            multiSelection: [],
            form:{
                Id: this.selecteditem.Id,
                reference: this.selecteditem.REFERENCE,
                comments: this.selecteditem.COMMENTS,
                method: this.selecteditem.METHOD,
                mode: this.mode
            }
        }
    },
    // props: ['showObsform', 'showReqform','reference', 'requester', 'comments', 'observations', 'Id', 'method', 'mode', 'obsMode', 'reqApproval', 'selecteditem'],
    props: ['showObsform', 'showReqform', 'observations', 'mode', 'obsMode', 'reqApproval', 'selecteditem'],
    methods: {
        closeForm: function(){
            this.$emit('update:showReqform', false);
            //this.$emit('update:select', 0);
        },
        newObservation: function(){
            this.$emit('update:activeObs', {});
            this.$emit('update:obsMode', 'insert');
            this.$emit('update:showObsform', true);
            this.$emit('update:showReqform', false);
        },
        postData: function(){
            var app = this
            app.progress=true;
            this.$http.post(
                "test", this.form
            )
            .then(
                function(status){
                    if(status.status = 200){
                        app.$emit('refresh');
                        console.log(status);
                    }
                }
            )
            .catch((e) => 
            {
                app.progress = false;
                alert(e);
            });
        },

        onSelect: function(items){
            console.log(this.filteredObs);
            this.multiSelection = items;
        },
        
        updateObs: function(item){
            this.$emit('update:activeObs', item);
            this.$emit('update:obsMode', 'update');
            this.$emit('update:showObsform', true);
            this.$emit('update:showReqform', false);
        },
        
        approveObs: function(){
            this.progress=true;
            var ids = [];
            for(var x in this.multiSelection){
                ids.push(this.multiSelection[x].Id)
            }
            this.$http.post(
                "approveObs", {'Id':ids}
            )
            // .then(
            //     function(status){
            //         console.log(status);
            //         if(status.data = 'Data Inserted...'){
            //             this.$emit('update:showReqform', false);
            //             this.$parent.refresh();
            //             this.$emit('update:search', null)
            //         }
            //      this.progress=false;
            // });
        },
        
        approveReq: function(){
            this.progress=true;
            this.$http.post(
                "http://hon.local/insert.php", {'Id':this.Id, 'mode':'approve','reference':this.reference, 'requester':this.requester, 'comments':this.comments}
            )
            .then(
                function(status){
                    console.log(status);
                    if(status.data = 'Data Inserted...'){
                        this.$emit('update:showReqform', false);
                        this.$parent.refresh();
                        this.$emit('update:search', null)
                    }
                 this.progress=false;
            });
        },

    },
    
    computed:{
        filteredObs: function(){
            return this.observations.filter((observation) => {
                return observation.REQUEST_ID == this.form.Id
            });
        },
        
        obsApprovalCheck: function(){
            for (var x in this.filteredObs){
                if (this.filteredObs[x].APPROVED == 0){
                    return true;
                    console.log(x);
                }
            }
            return false;
        },
    },
}
</script>
<style scoped>
    .update{
        text-align: right;
    }
</style>
