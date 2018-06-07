<template>
  <div>
    <v-dialog 
        :value="dialog"
        max-width="1000px"
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
                                <v-flex xs6>
                                    <v-text-field v-model="form.reference" label="Reference" :disabled="form.status=='Approved'"></v-text-field>
                                </v-flex>
                                <v-flex xs6>
                                    <v-select
                                        :disabled="form.status=='Approved'"
                                        :items="['San Dimas', 'La Verne']"
                                        v-model="form.location"
                                        label="location"
                                        ></v-select>
                                </v-flex> 
                                <v-flex xs12>
                                    <v-text-field v-model="form.comments" label="Comments" :disabled="form.status=='Approved'"></v-text-field>
                                </v-flex>
                                <v-flex xs12>
                                    <v-text-field v-model="form.method" label="Method" :disabled="form.status=='Approved'"></v-text-field>
                                </v-flex>
                                <v-flex xs12>
                                    <v-select
                                        multiple
                                        chips
                                        :items="sampleTypes"
                                        v-model="sample_type"
                                        label="Sample Type(s)"
                                        :disabled="form.status=='Approved'"
                                        ></v-select>
                                </v-flex>
                                <v-flex xs12>
                                    <v-text-field v-if="form.prnumber != null || sample_type.indexOf('Event Related') > -1" v-model="form.prnumber" label="PR #"
                                        :disabled="form.status=='Approved'"></v-text-field>
                                </v-flex>                                
                                <v-flex xs6>
                                    <v-menu
                                        ref="menu"
                                        :close-on-content-click="false"
                                        v-model="menu"
                                        :return-value.sync="form.due_on"
                                        lazy
                                        transition="scale-transition"
                                        offset-y
                                        :disabled="form.status=='Approved'"
                                    >
                                        <v-text-field
                                            slot="activator"
                                            v-model="form.due_on"
                                            label="Due Date"
                                        ></v-text-field>
                                        <v-date-picker v-model="form.due_on" @input="$refs.menu.save(form.due_on)"></v-date-picker>
                                    </v-menu>
                                </v-flex>
                                <v-flex xs6>
                                    <v-select
                                        :disabled="!$auth.check('reviewer') || form.status=='Approved'"
                                        :items="users"
                                        v-model="form.assigned_reviewer"
                                        label="Assigned Reviewer"
                                        ></v-select>
                                </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-text>
                <v-divider></v-divider>
                <div v-bind:class="[ selecteditem.status == 'Approved' ? 'alert-success' : 'alert-danger', 'px-2']">
                    <p>Current status: {{ selecteditem.status }}</p>
                </div>
                <v-card-actions>
                    <v-select
                    v-if="title != 'New'"
                    :items="$auth.check('reviewer') ? ['In Progress', 'Waiting on Someone', 'Approved'] : ['Submitted']"
                    :disabled="$auth.check('analyst') && !!selecteditem.approved"
                    v-model="form.status"
                    label="Update Status"
                    ></v-select>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" flat @click.native="close">Cancel</v-btn>
                    <v-btn color="blue darken-1" flat @click.native="postData" :disabled="form.status=='Approved' && !!selecteditem.approved">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-tab-item>
        <v-tab-item :key=2>
            <v-card>
                <v-card-text>
                    <v-data-table
                        :headers="headers"
                        :items="observations"
                        item-key="id"
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
                                        :disabled="!!props.item.approved"
                                    ></v-checkbox>
                                </td>
                                <td>{{ props.item.reference }}</td>
                                <td>{{ props.item.observation }}</td>
                                <td>{{ props.item.actions }}</td>
                                <td>{{ props.item.criticality }}</td>
                                <td>{{ props.item.response }}</td>
                                <td class="justify-center layout px-0">
                                    <v-btn v-if="!props.item.approved" icon class="mx-0" @click.native="editObservation(props.item)">
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
                    <v-btn v-if="!filteredSelected.length && $auth.check('reviewer')" color="blue darken-1" flat @click.native="newObservation" :disabled="!!this.selecteditem.approved">Add Observation</v-btn>
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
                'Test Method Transfer',
                'Event Related',
            ],
            headers: [
                {text: 'Reference', value: 'reference'},
                {text: 'Observation', value: 'observation'},
                {text: 'Actions', value: 'actions'},
                {text: 'Criticality', value: 'criticality'},
                {text: 'Response', value: 'response'},
                {text: 'Edit', value: 'id', sortable: false},
            ],
            showObsForm: false,
            form: {
                mode: '',
                due_on: null,
                status: 'Completed',
            },
            sample_type: null,
            selected: [],   
            menu: false,         
        }
    },
    props:['dialog', 'selecteditem', 'users'],
    methods:{
        refresh: function () {
            this.search = null;
            if(this.selecteditem.id){
                this.$http.get(`/requests/${this.selecteditem.id}/observations`)
                .then(res => {
                    this.observations = res.data.observations;
                });
                // if(obs) {
                //         this.showObsform = false;
                //         this.showReqform = true;
                //     }
                // else {
                //     this.dialog = false;
            }
        },

        close(){
            this.$emit('update:dialog', false);
        },
        
        postData: function(){
            if(this.sample_type.length > 0){
                this.form.sample_type = JSON.stringify(this.sample_type);
            }
            else{
                this.form.sample_type = null;
            }

            if(this.form.status == 'Approved'){
                for (var x in this.observations){
                    if (!this.observations[x].approved){
                        alert('Please approve all observations prior to approving the record!');
                        return;
                    }
                }
            }

            var app = this
            // app.progress=true;
            this.$http.post(
                `${this.selecteditem.id ? 'requests/'+`${this.selecteditem.id}`+'/update' : 'requests/create'}`, this.form
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
            this.observation = {request_id: this.selecteditem.id, reference: this.selecteditem.reference};
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
                if (!this.selected[x].approved){
                    ids.push(this.selected[x].id)
                };
            }
            this.$http.post(
                "approveObs", {'id':ids}
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
            if (this.selecteditem.id){
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
                return !item.approved
            });
        },
    },
    created() {
        this.refresh();
        Object.assign(this.form, this.selecteditem);
        if(this.form.sample_type){
            this.sample_type = JSON.parse(this.form.sample_type);
        }
        else{
            this.sample_type = [];
        }
    }
}
</script>
