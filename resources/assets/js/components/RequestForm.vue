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
                            <!-- <v-form :disabled="selecteditem.APPROVED"> -->
                                <v-flex xs12 sm6 md4>
                                    <v-text-field v-model="form.REFERENCE" label="Reference" :disabled="form.STATUS=='Approved'"></v-text-field>
                                </v-flex>
                                <v-flex xs12 sm6 md4>
                                    <v-text-field v-model="form.COMMENTS" label="Comments" :disabled="form.STATUS=='Approved'"></v-text-field>
                                </v-flex>
                                <v-flex xs12 sm6 md4>
                                    <v-text-field v-model="form.METHOD" label="Method" :disabled="form.STATUS=='Approved'"></v-text-field>
                                </v-flex>
                                <v-flex xs12 sm6 md4>
                                    <v-select
                                        multiple
                                        chips
                                        :items="sampleTypes"
                                        v-model="SAMPLE_TYPE"
                                        label="Sample Type(s)"
                                        :disabled="form.STATUS=='Approved'"
                                        ></v-select>
                                </v-flex>
                                <v-flex xs12 sm6 md4>
                                    <v-text-field v-if="form.PRNUMBER != null || SAMPLE_TYPE.indexOf('Event Related') > -1" v-model="form.PRNUMBER" label="PR #"
                                        :disabled="form.STATUS=='Approved'"></v-text-field>
                                </v-flex>
                                <v-flex xs12 sm6 md4>
                                    <v-select
                                        :disabled="!$auth.check('reviewer') || form.STATUS=='Approved'"
                                        :items="users"
                                        v-model="form.ASSIGNED_REVIEWER"
                                        label="Assigned Reviewer"
                                        ></v-select>
                                </v-flex>
                                <v-flex xs12 sm6 md4>
                                    <v-menu
                                        ref="menu"
                                        :close-on-content-click="false"
                                        v-model="menu"
                                        :return-value.sync="form.DATE_DUE"
                                        lazy
                                        transition="scale-transition"
                                        offset-y
                                        :disabled="form.STATUS=='Approved'"
                                    >
                                        <v-text-field
                                            slot="activator"
                                            v-model="form.DATE_DUE"
                                            label="Due Date"
                                        ></v-text-field>
                                        <v-date-picker v-model="form.DATE_DUE" @input="$refs.menu.save(form.DATE_DUE)"></v-date-picker>
                                    </v-menu>
                                </v-flex>
                            <!-- </v-form> -->
                        </v-layout>
                    </v-container>
                </v-card-text>
                <v-divider></v-divider>
                <div v-bind:class="[ selecteditem.STATUS == 'Approved' ? 'alert-success' : 'alert-danger']">
                    <p>Current status: {{ selecteditem.STATUS }}</p>
                </div>
                <v-card-actions>
                    <v-select
                    v-if="title != 'New'"
                    :items="$auth.check('reviewer') ? ['Redo', 'Approved'] : ['Completed']"
                    :disabled="$auth.check('analyst') && !!selecteditem.APPROVED"
                    v-model="form.STATUS"
                    label="Update Status"
                    ></v-select>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" flat @click.native="close">Cancel</v-btn>
                    <v-btn color="blue darken-1" flat @click.native="postData" :disabled="form.STATUS=='Approved' && !!selecteditem.APPROVED">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-tab-item>
        <v-tab-item :key=2>
            <v-card>
                <v-card-text>
                    <v-data-table
                        :headers="headers"
                        :items="observations"
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
                                        :disabled="!!props.item.APPROVED"
                                    ></v-checkbox>
                                </td>
                                <td>{{ props.item.REFERENCE }}</td>
                                <td>{{ props.item.OBSERVATION }}</td>
                                <td>{{ props.item.ACTIONS }}</td>
                                <td>{{ props.item.RESPONSE }}</td>
                                <td class="justify-center layout px-0">
                                    <v-btn v-if="!props.item.APPROVED" icon class="mx-0" @click.native="editObservation(props.item)">
                                        <v-icon color="teal">edit</v-icon>
                                    </v-btn>
                                    <v-icon v-else color="teal">done</v-icon>
                                </td>
                            <!-- </tr> -->
                        </template>
                    </v-data-table>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" flat @click.native="close">Close</v-btn>
                    <v-btn v-if="!filteredSelected.length && $auth.check('reviewer')" color="blue darken-1" flat @click.native="newObservation" :disabled="!!this.selecteditem.APPROVED">Add Observation</v-btn>
                    <v-btn v-else-if="!!filteredSelected.length && $auth.check('reviewer')" color="blue darken-1" flat @click.native="approveObs">Approve Observation{{filteredSelected.length>1 ? 's' : ''}}</v-btn>
                </v-card-actions>
            </v-card>
        </v-tab-item>
      </v-tabs>
    </v-dialog>
  <observation-form @refresh="refresh" v-if="showObsForm" :showObsForm.sync="showObsForm" :observation="observation"></observation-form>
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
            observations:[],
            sampleTypes: [
                'In Process',
                'Raw Material',
                'Finished Product',
                'Stability',
                'Surveillance',
                'Utilities',
                'Instrumentation',
                'Training',
                'Validation',
                'Preparation',
                'CTL',
                'Event Related',
            ],
            headers: [
                {text: 'Reference', value: 'REFERENCE'},
                {text: 'Observation', value: 'OBSERVATION'},
                {text: 'Actions', value: 'ACTIONS'},
                {text: 'Response', value: 'RESPONSE'},
                {text: 'Edit', value: 'Id', sortable: false},
            ],
            showObsForm: false,
            form: {
                mode: '',
                DATE_DUE: null,
                STATUS: 'Completed',
            },
            SAMPLE_TYPE: null,
            selected: [],   
            menu: false,         
        }
    },
    props:['dialog', 'selecteditem', 'users'],
    methods:{
        refresh: function () {
            this.search = null;
            this.$http.get('/getobservations', {params: {"Id": this.selecteditem.Id}})
            .then(res => {
                this.observations = res.data.observations;
            });
            // if(obs) {
            //         this.showObsform = false;
            //         this.showReqform = true;
            //     }
            // else {
            //     this.dialog = false;
        },

        close(){
            this.$emit('update:dialog', false);
        },
        
        postData: function(){
            if(this.SAMPLE_TYPE.length > 0){
                this.form.SAMPLE_TYPE = JSON.stringify(this.SAMPLE_TYPE);
            }
            else{
                this.form.SAMPLE_TYPE = null;
            }

            if(this.form.STATUS == 'Approved'){
                for (var x in this.observations){
                    if (!this.observations[x].APPROVED){
                        alert('Please approve all observations prior to approving the record!');
                        return;
                    }
                }

                this.approveRequest();

            }

            var app = this
            // app.progress=true;
            this.$http.post(
                "test", this.form
            )
            .then(
                function(status){
                    if(status.status = 200){
                        app.$emit('refresh');
                        app.$emit('update:dialog', false);
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
            this.observation = {REQUEST_ID: this.selecteditem.Id, REFERENCE: this.selecteditem.REFERENCE};
            this.showObsForm = true;
        },
        
        editObservation: function (item) {
            this.observation = item;
            this.showObsForm = true;
        },
        
        approveObs: function () {
            this.progress=true;
            var ids = [];
            
            for(var x in this.selected){
                if (!this.selected[x].APPROVED){
                    ids.push(this.selected[x].Id)
                };
            }
            this.$http.post(
                "approveObs", {'Id':ids}
            )
            .then(status => 
                {
                    if(status.status = 200){
                        this.refresh();
                        this.selected = [];
                    }
                }
            )
            .catch(e =>
            {
                alert(e);
            });
        },

        approveRequest: function () {
            this.$http.post(
                "approveRequest", this.selecteditem
            )
            .then(
                // status => 
                // {
                //     if(status.status = 200){
                //         this.$emit('refresh');
                //         this.$emit('update:dialog', false);
                //     }
                // }
            )
            .catch(e =>
            {
                alert(e);
            });
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

        filteredSelected: function() {
            return this.selected.filter(item => {
                return !item.APPROVED
            });
        },
    },
    created() {
        this.refresh();
        Object.assign(this.form, this.selecteditem);
        if(this.form.SAMPLE_TYPE){
            this.SAMPLE_TYPE = JSON.parse(this.form.SAMPLE_TYPE);
        }
        else{
            this.SAMPLE_TYPE = [];
        }
    }
}
</script>
