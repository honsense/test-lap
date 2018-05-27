<template>
    <div>
        <md-dialog :md-active="showObsform" class="Obsform">
            <md-dialog-title>Observation</md-dialog-title>
            <div>
                <md-progress-bar md-mode="indeterminate" v-show="progress"></md-progress-bar>
            </div>

            <form nonvalidate @submit.prevent="validateUser">
                <md-card >
                    <md-field>
                        <label for="reference">Reference</label>
                        <md-input name="reference" id="reference" :value="activeObs.REFERENCE" disabled></md-input>
                    </md-field>
                    <md-field :class="getValidationClass('requester')">
                        <label for="requester">Requester</label>
                        <md-input name="requester" id="requester" v-model="form.requester"></md-input>
                        <span class="md-error" v-if="!$v.form.requester.required">This field is required</span>
                    </md-field>
                    <md-field :class="getValidationClass('observation')">
                        <label for="observation">Observation</label>
                        <md-textarea name="observation" id="observation" v-model="form.observation" md-autogrow></md-textarea>
                        <span class="md-error" v-if="!$v.form.observation.required">This field is required</span>

                    </md-field>
                    <md-field :class="getValidationClass('action')">
                        <label for="action">Action</label>
                        <md-textarea name="action" id="action" v-model="form.action" md-autogrow></md-textarea><!--resulme here-->
                        <span class="md-error" v-if="!$v.form.action.required">This field is required</span>
                    </md-field>
                    <md-field >
                        <label for="response">Response</label>
                        <md-textarea name="response" id="response" v-model="form.response" md-autogrow></md-textarea>
                        <!-- <span class="md-error" v-if="!$v.form.action.required">This field is required</span> -->
                    </md-field>

                    <md-card-actions>
                        <md-button @click="closeForm" class="md-primary">Close</md-button>
                        <md-button type="submit" class="md-primary">Submit</md-button>
                    </md-card-actions>
                </md-card>
            </form>

<!--            <md-dialog-actions>
                <md-button class="md-primary" @click="closeForm">Close</md-button>
                <md-button type="submit" class="md-primary" @click="postData">Save</md-button>
            </md-dialog-actions>
-->
        </md-dialog>
    </div>
</template>

<script>
import { validationMixin } from 'vuelidate'
import {
    required
} from 'vuelidate/lib/validators'
export default {
    name: 'obsview',
    mixins: [validationMixin],
    data() {
        return {
            form: {
                requester: null,
                observation: this.activeObs.OBSERVATION,
                action: this.activeObs.ACTIONS,
                response: this.activeObs.RESPONSE,
            },
            progress:false
        }
    },

    props: ['showObsform','reference', 'requester', 'comments', 'observations', 'Id', 'obsMode', 'activeObs'],
    validations: {
        form: {
            requester: {
                required
            },
            observation: {
                required
            },
            action: {
                required
            }
        },
        sending: false
    },
    methods: {
        getValidationClass (fieldName) {
            const field = this.$v.form[fieldName]

            if (field) {
                return {
                'md-invalid': field.$invalid && field.$dirty
                }
            }
        },
        validateUser() {
            this.$v.$touch()

            if (!this.$v.$invalid) {
                this.postData()
            }
        },
        closeForm: function (){
            console.log(this.activeObs)
            this.$v.$reset()
            this.form.requester = null;
            this.form.observation = null;
            this.form.action = null;
            this.$emit('update:showObsform', false);
            this.$emit('update:showReqform', true); //update these
        },
        postData: function() {
            this.progress=true;
            this.$http.post(
                "http://hon.local/obsinsert.php", {'Id':this.activeObs.Id, 'mode':this.obsMode, 'reference':this.reference, 'observation':this.form.observation, 'action':this.form.action, 'response':this.form.response}
            )
            .then(
                function(status){
                    console.log(status);
                    if(status.data = 'Data Inserted...'){
                        // this.$emit('update:showObsform', false);
                        this.$parent.refresh(1);
                    }
                this.progress=false;
            });
            //this.closeForm();
        },
    },
}
</script>

<style scoped>

        .Obsform{
            padding:20px;
        }
</style>