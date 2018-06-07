<template>
<div>
    <v-card>
        <v-card-title>
            Requests
            <v-spacer></v-spacer>
                <v-text-field
                    v-model="search"
                    append-icon="search"
                    label="Search"
                    single-line
                    hide-details
                    clearable
                    max-width="10px"
                >
                </v-text-field>
        </v-card-title>
  <request-form v-if="dialog" @refresh="refresh" :dialog.sync="dialog" :selecteditem="selecteditem" :users="users"></request-form>    
  <v-data-table
    :headers="headers"
    :items="sources"
    item-key="id"
    disable-initial-sort
    class="elevation-1"
    :search="search"
    :rows-per-page-items="[10, 15, 50,{'text':'All', 'value': -1}]"
  >
    <template slot="items" slot-scope="props">
    <tr @click="onSelect(props.item)">
      <td>{{ props.item.reference }}</td>
      <td>{{ props.item.requester }}</td>
      <td>{{ props.item.comments }}</td>
      <td>{{ props.item.method }}</td>
      <td>{{ props.item.created_at }}</td>
      <td>{{ props.item.due_on }}</td>
      <td>{{ props.item.assigned_reviewer }}</td>
    </tr>
    </template>
    <template slot="no-results" :value="true">
            <v-container fluid grid-list-xl fill-height>
                <v-layout row justify-space-between>
                    <v-flex></v-flex>
                    <v-flex>
                        <h3 class="display-3">No records found</h3>
                        <span class="subheading">No entries matching {{ search }} were found</span>
                        <v-divider class="my-3"></v-divider>
                        <div class="title mb-3">Create a new record below</div>
                        <v-btn @click="newRequest" color="primary" dark>New Request</v-btn>
                    </v-flex>
                    <v-flex></v-flex>
                </v-layout>
            </v-container>
    </template>
    <template slot="no-data" :value="true">
            <v-container fluid grid-list-xl fill-height>
                <v-layout row justify-space-between>
                    <v-flex></v-flex>
                    <v-flex>
                        <h3 class="display-3">No records found</h3>
                        <v-divider class="my-3"></v-divider>
                        <div class="title mb-3">Create a new record below</div>
                        <v-btn @click="newRequest" color="primary" dark>New Request</v-btn>
                    </v-flex>
                    <v-flex></v-flex>
                </v-layout>
            </v-container>
    </template>
  </v-data-table>
    </v-card>
</div>
</template>


<script>
import RequestForm from './RequestForm'

const toLower = text => {
    return text.toString().toLowerCase()
}

const searchByRef = (items, term) => {
    if (term) {
        return items.filter(item => toLower(item.REFERENCE).includes(toLower(term)))
    }
    return items
}

export default {
    name: 'tableTemplate',
    components:{
        RequestForm,
    },
    data() {
        return{
            dialog: false,
            headers: [
                {text: 'Reference', value: 'reference'},
                {text: 'Requester', value: 'requester'},
                {text: 'Comments', value: 'comments'},
                {text: 'Method', value: 'method'},
                {text: 'Date Requested', value: 'created_at'},
                {text: 'Date Due', value: 'due_on'},
                {text: 'Assigned Reviewer', value: 'assigned_reviewer'},
            ], 
            sources: [],
            progress: true,
            search: null,
            searched: [],
            selecteditem: {},
        }
    },
    methods: {
        searchUpdate: function() {
            this.searched = searchByRef(this.sources, this.search);
        },
        onSelect: function(item){
            this.$http.get(`/requests/${item.id}`)
            .then(res =>
            {
                if(res.status = 200){
                    this.selecteditem = res.data.request;
                    this.dialog = true;
                }
                else {
                    return;
                }
            });
        },
        newRequest: function() {
            this.dialog = true;
            this.selecteditem = {reference: this.search}; //may just use {} instead of REFERENCE: this.search
        },
        refresh: function (obs) {
            this.search = null;
            this.$http.get('/requests')
            .then(res => {
                this.sources = res.data.requests;
                this.users = res.data.users.map(x => {return x.username});
                this.searched = this.sources;
                this.progress = false;
            });
        },
    },
    mounted: function () {
        this.refresh();
    },
    computed:{
        filteredItems: function(){
            return this.sources.filter((item)=>{
                return item.reference.match(this.search)
            });
        }
    }
}
</script>

<style scoped>

    /* .app{
        margin-top: 60px;
        margin-left: 30px;
        margin-right: 30px;
        margin-bottom: 60px; 
    } */
</style>