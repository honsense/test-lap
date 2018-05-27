<template>
  <div>
    <v-dialog 
        :value="dialog"
        max-width="700px"
        persistent
        lazy
    >
     <!-- <v-btn slot="activator" color="primary" dark>Open Dialog</v-btn> -->

        <v-tabs>
            <v-tab>
                Request
            </v-tab>
            <v-tab>
                Observation
            </v-tab>
        <v-tab-item :key=1>
            <v-card>
                <v-card-title>
                    <span class="headline">{{ title }}</span>
                </v-card-title>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-layout wrap>
                            <v-flex xs12 sm6 md4>
                                <v-text-field v-model="form.REFERENCE" label="Reference"></v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md4>
                                <v-text-field v-model="form.REQUESTER" label="Requester"></v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md4>
                                <v-text-field v-model="form.COMMENTS" label="Comments"></v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md4>
                                <v-text-field v-model="form.METHOD" label="Method"></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" flat @click.native="close">Cancel</v-btn>
                    <v-btn color="blue darken-1" flat @click.native="postData">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-tab-item>
        <v-tab-item :key=2>
            <v-card>
                <v-card-text>
                    <v-data-table
                        :headers="headers"
                        :items="filteredObs"
                        item-key="Id"
                        disable-initial-sort
                        hide-actions
                        select-all
                        v-model="selected"
                        class="elevation-1"
                        >
                        <template slot="items" slot-scope="props">
                            <!-- <tr> -->
                                <td>
                                    <v-checkbox
                                        v-model="props.selected"
                                        primary
                                        hide-details
                                    ></v-checkbox>
                                </td>
                                <td>{{ props.item.REFERENCE }}</td>
                                <td>{{ props.item.OBSERVATION }}</td>
                                <td>{{ props.item.ACTIONS }}</td>
                                <td>{{ props.item.RESPONSE }}</td>
                                <td class="justify-center layout px-0">
                                    <v-btn icon class="mx-0" @click.native="derp(props.item)">
                                        <v-icon color="teal">edit</v-icon>
                                    </v-btn>
                                </td>
                            <!-- </tr> -->
                        </template>
                    </v-data-table>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" flat @click.native="close">Close</v-btn>
                    <v-btn v-if="!selected.length && $auth.check('reviewer'|'admin')" color="blue darken-1" flat @click.native="newObservation">Add Observation</v-btn>
                    <v-btn v-else-if="!!selected.length && $auth.check('reviewer'|'admin')" color="blue darken-1" flat @click.native="approveObs">Approve Observation{{selected.length>1 ? 's' : ''}}</v-btn>
                </v-card-actions>
            </v-card>
        </v-tab-item>
      </v-tabs>
    </v-dialog>
  <observation-form v-if="showObsForm" :showObsForm.sync="showObsForm" :observation="observation"></observation-form>
  </div>
</template>

<script>
import ObservationForm from './ObservationForm.vue'

export default {
    components: {
        ObservationForm
        },
    data (){
        return{
            headers: [
                {text: 'Reference', value: 'REFERENCE'},
                {text: 'Observation', value: 'OBSERVATION'},
                {text: 'Actions', value: 'ACTIONS'},
                {text: 'Response', value: 'RESPONSE'},
                {text: 'Edit', value: 'Id', sortable: false},
                // {text: 'Date Requested', value: 'DATE_REQUESTED'},
            ],
            showObsForm: false,
            form: {
                mode: '',
            },
            selected: [],
        }
    },
    props:['dialog', 'selecteditem', 'observations'],
    methods:{
        derp(item){
            this.observation = item
            console.log('we did it');
            this.showObsForm = true;
        },
        close(){
            this.$emit('update:dialog', false);
        },
        
        postData: function(){
            var app = this
            // app.progress=true;
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
        newObservation: function () {
            this.observation = {};
            this.showObsForm = true;
        },
        approveObs: function () {
            this.progress=true;
            var ids = [];
            
            for(var x in this.selected){
                ids.push(this.selected[x].Id)
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
    },
    computed:{
        title: function () {
            if (this.selecteditem.Id){
                this.form.mode = 'update';
                return "Edit"
            }
            else {
                this.form.mode = 'insert';
                return "New"
            }
        },
        filteredObs: function(){
            return this.observations.filter((observation) => {
                return observation.REQUEST_ID == this.form.Id
            });
        },
    },
    created() {
        Object.assign(this.form, this.selecteditem);
    }
}
</script>
