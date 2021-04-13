<!--suppress ALL -->
<template>
    <div class="container">
        <v-snackbar v-model="snackbar" :timeout="snackout">
            {{ snacktext }}

            <template v-slot:action="{ attrs }">
                <v-btn color="blue" text v-bind="attrs" @click="snackbar = false" >
                    Close
                </v-btn>
            </template>
        </v-snackbar>

        <v-dialog v-model="loading" persistent width="40%">
            <v-card color="primary" dark class="pa-4">
                <v-card-text style="color: white; padding: 20px !important">
                    <div><span style="font-weight: bold; font-size: large">Loading... </span></div>
                    <v-progress-linear
                            indeterminate
                            color="white"
                            class="mb-0"
                    ></v-progress-linear>
                </v-card-text>
            </v-card>
        </v-dialog>
        
        <v-dialog
                v-model="peripheralDetailsDialog"
                max-width="50%"
                persistent
        >
            <v-card>
                <v-card-title>Peripheral Details</v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-row>
                        <v-col cols="3">
                            <!--<v-text-field :disabled="!peripheralDetails.canEdit" type="number" v-model="peripheralDetails.uid" placeholder="UID" label="UID"></v-text-field>-->
                            <v-text-field :disabled="!peripheralDetails.canEdit" v-model="peripheralDetails.uid" placeholder="UID" label="UID"></v-text-field>
                        </v-col>
                        <v-col cols="3">
                            <v-text-field :disabled="!peripheralDetails.canEdit" v-model="peripheralDetails.vendor" placeholder="Vendor" label="Vendor"></v-text-field>
                        </v-col>
                        <v-col cols="3">
                            <v-text-field :disabled="true" v-model="peripheralDetails.created" placeholder="Created" label="Created"></v-text-field>
                        </v-col>
                        <v-col cols="3">
                            <v-switch
                                    :disabled="!peripheralDetails.canEdit"
                                    v-model="peripheralDetails.status"
                                    :label="peripheralDetails.status ? 'Online' : 'Offline'"
                            ></v-switch>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn color="secondary" @click="peripheralDetailsDialog = false">Close</v-btn>
                    <v-btn v-show="peripheralDetails.canEdit" color="success" @click="storePeripheral(peripheralDetails)">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog
                v-model="errorDialog"
                max-width="30%"
                persistent
        >
            <v-card>
                <v-card-title>Attention:</v-card-title>
                <v-card-text >
                    <v-row class="ml-2 mt-1">
                        <div v-html="errorMessage"></div>
                    </v-row>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn color="primary" @click="errorDialog = false">OK</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog
                v-model="deletionData.dialog"
                max-width="30%"
        >
            <v-card>
                <v-card-title>Confirm Action</v-card-title>
                <v-card-text >
                    <v-row class="ml-2 mt-1">
                        <p>Deleting an item is permanent. Proceed?</p>
                    </v-row>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn color="secondary" @click="deletionData.dialog = false">CANCEL</v-btn>
                    <v-btn color="error" @click="deleteElement()">PROCEED</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog
                v-model="gatewayDetailsDialog"
                scrollable
                max-width="50%"
                persistent
        >
            <v-card>
                <v-card-title>Gateway Details</v-card-title>
                <v-divider></v-divider>
                <v-card-text style="max-height: 300px;">
                    <v-row>
                        <v-col cols="4">
                            <v-text-field :disabled="!gatewayDetails.canEdit" v-model="gatewayDetails.serial" placeholder="Unique Serial" label="Serial"></v-text-field>
                        </v-col>
                        <v-col cols="4">
                            <v-text-field :disabled="!gatewayDetails.canEdit" v-model="gatewayDetails.name" placeholder="Name" label="Name"></v-text-field>
                        </v-col>
                        <v-col cols="4">
                            <v-text-field :disabled="!gatewayDetails.canEdit" v-model="gatewayDetails.ipv4" placeholder="IPv4" label="IPv4"></v-text-field>
                        </v-col>
                        <v-col cols="12" class="pa-6" v-show="gatewayDetails._id !== null">
                            <v-row>
                                <span style="font-weight: bold;">Peripherals</span>
                            </v-row>
                            <v-row>
                                <v-btn v-show="gatewayDetails.canEdit && gatewayDetails.peripherals.length < 10" @click="promptNewPeripheral(gatewayDetails._id)" small color="primary"><v-icon>add</v-icon> New Peripheral</v-btn>
                            </v-row>
                            <v-row>
                                <v-data-table
                                        :hide-default-footer="true"
                                        style="width: 100%;"
                                        :headers="headersPeripherals"
                                        :items="gatewayDetails.peripherals"
                                        class="elevation-0"
                                >
                                    <template v-slot:item="props">
                                        <tr>
                                            <td>{{props.item.uid}}</td>
                                            <td>{{props.item.vendor}}</td>
                                            <td>{{props.item.created}}</td>
                                            <td>
                                                <span v-if="props.item.status == true" style="color: green;">ONLINE</span>
                                                <span v-else style="color: red;">OFFLINE</span>
                                            </td>
                                            <td class="text-center">
                                                <v-btn @click="showPeripheralDetails(props.item, false)" small color="primary"><v-icon>info</v-icon></v-btn>
                                                <v-btn @click="showPeripheralDetails(props.item, true)" small color="warning" v-show="gatewayDetails.canEdit"><v-icon>edit</v-icon></v-btn>
                                                <v-btn small color="error" v-show="gatewayDetails.canEdit" @click="promptDeletionDialog(props.item._id, props.item.id_gateway)"><v-icon>delete</v-icon></v-btn>
                                            </td>
                                        </tr>
                                    </template>
                                </v-data-table>
                            </v-row>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn color="secondary" @click="gatewayDetailsDialog = false">Close</v-btn>
                    <v-btn v-show="gatewayDetails.canEdit"  color="success" @click="storeGateway(gatewayDetails)">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-container>
            <v-card class="mx-auto pa-4">
                <v-card-title>Gateway Management</v-card-title>
                <v-row>
                    <v-divider></v-divider>
                </v-row>
                <v-row>
                    <v-col>
                        <v-btn @click="promptNewGateway()" color="primary"><v-icon>add</v-icon> New Gateway</v-btn>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col>
                        <v-data-table
                                :headers="headers"
                                :items="gateways"
                                :items-per-page="10"
                                class="elevation-0"
                        >
                            <template v-slot:item="props">
                                <tr>
                                    <td>{{props.item.serial}}</td>
                                    <td>{{props.item.name}}</td>
                                    <td>{{props.item.ipv4}}</td>
                                    <td>{{props.item.peripherals.length}}</td>
                                    <td class="text-center">
                                        <v-btn small color="primary" @click="showGatewayDetails(props.item, false)"><v-icon>info</v-icon></v-btn>
                                        <v-btn small color="warning" @click="showGatewayDetails(props.item, true)"><v-icon>edit</v-icon></v-btn>
                                        <v-btn small color="error" @click="promptDeletionDialog(props.item._id)"><v-icon>delete</v-icon></v-btn>
                                    </td>
                                </tr>
                            </template>
                        </v-data-table>
                    </v-col>
                </v-row>
                <v-card-actions>
                </v-card-actions>
            </v-card>
        </v-container>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.loadData();
        },
        data() {
            return {
                snackout: 2000,
                snackbar: false,
                snacktext: '',
                errorDialog: false,
                errorMessage: '',
                gatewayDetailsDialog: false,
                peripheralDetailsDialog: false,
                loading: false,
                gateways: [],
                deletionData: {
                    dialog: false,
                    id: null,
                    parent_id: null,
                },
                peripheralDetails: {
                    _id: null,
                    newItem: false,
                    canEdit: false,
                    uid: null,
                    vendor: '',
                    created: '',
                    status: false,
                },
                gatewayDetails: {
                    _id: null,
                    newItem: false,
                    canEdit: false,
                    name: '',
                    serial: '',
                    ipv4: '',
                    peripherals: [],
                },
                headersPeripherals: [
                    {
                        text: 'UID',
                        align: 'left',
                        sortable: true,
                        value: 'uid'
                    },
                    {
                        text: 'Vendor',
                        align: 'left',
                        sortable: true,
                        value: 'vendor'
                    },
                    {
                        text: 'Created',
                        align: 'left',
                        sortable: true,
                        value: 'created'
                    },
                    {
                        text: 'Status',
                        align: 'left',
                        sortable: true,
                        value: 'status'
                    },
                    {
                        text: 'Actions',
                        sortable: false,
                        align: 'center'
                    },
                ],
                headers: [
                    {
                        text: 'Serial',
                        align: 'left',
                        sortable: true,
                        value: 'serial'
                    },
                    {
                        text: 'Name',
                        align: 'left',
                        sortable: true,
                        value: 'name'
                    },
                    {
                        text: 'IPv4',
                        align: 'left',
                        sortable: true,
                        value: 'ipv4'
                    },
                    {
                        text: 'Peripherals',
                        align: 'left',
                        sortable: false,
                    },
                    {
                        text: 'Actions',
                        sortable: false,
                        align: 'center'
                    },
                ],
            };
        },
        computed: {
        },
        methods: {
            deleteElement() {
                if (this.deletionData.parent_id == null)
                    this.destroyGateway(this.deletionData.id);
                else
                    this.destroyPeripheral(this.deletionData.id, this.deletionData.parent_id);

                this.deletionData.dialog = false;
                this.deletionData.id = null;
                this.deletionData.parent_id = null;
            },
            promptDeletionDialog(id, parent_id = null) {
                this.deletionData.id = id;
                this.deletionData.parent_id = parent_id;
                this.deletionData.dialog = true;
            },
            snackIT(msg) {
                this.snacktext = msg;
                this.snackbar = true;
            },
            alertIT(msg) {
                this.errorMessage = msg;
                this.errorDialog = true;
            },
            promptNewPeripheral(id_gateway) {
                this.peripheralDetails.created = '';
                this.peripheralDetails.newItem = true;
                this.peripheralDetails.id_gateway = id_gateway;
                this.peripheralDetails.status = false;
                this.peripheralDetails.uid = null;
                this.peripheralDetails._id = null;
                this.peripheralDetails.vendor = '';
                this.peripheralDetails.canEdit = true;
                this.peripheralDetailsDialog = true;
            },
            promptNewGateway() {
                this.gatewayDetails.peripherals = [];
                this.gatewayDetails._id = null;
                this.gatewayDetails.ipv4 = '';
                this.gatewayDetails.name = '';
                this.gatewayDetails.serial = '';
                this.gatewayDetails.newItem = true;
                this.gatewayDetails.canEdit = true;
                this.gatewayDetailsDialog = true;
            },
            showPeripheralDetails(item, edit) {
                this.peripheralDetails.created = item.created;
                this.peripheralDetails.newItem = false;
                this.peripheralDetails.id_gateway = item.id_gateway;
                this.peripheralDetails.status = item.status;
                this.peripheralDetails.uid = item.uid;
                this.peripheralDetails._id = item._id;
                this.peripheralDetails.vendor = item.vendor;
                this.peripheralDetails.canEdit = edit;
                this.peripheralDetailsDialog = true;
            },
            showGatewayDetails(item, edit) {
                this.gatewayDetails.peripherals = item.peripherals;
                this.gatewayDetails._id = item._id;
                this.gatewayDetails.ipv4 = item.ipv4;
                this.gatewayDetails.name = item.name;
                this.gatewayDetails.serial = item.serial;
                this.gatewayDetails.newItem = false;
                this.gatewayDetails.canEdit = edit;
                this.gatewayDetailsDialog = true;
            },
            loadData() {
                this.indexGateways();
            },
            indexGateways() {
                let self = this;
                this.loading = true;
                axios.post('/indexGateways').then(response => {
                    if (response.data.success)
                        self.gateways = response.data.payload;
                    else
                        self.alertIT(response.data.message);
                    self.loading = false;
                })
                    .catch(function (error) {
                        self.parseErrors(error);
                        self.loading = false;
                    });
            },
            indexPeripherals(id_gateway) {
                let self = this;
                this.loading = true;
                axios.post('/indexPeripherals', {_id: id_gateway}).then(response => {
                    if (response.data.success)
                        self.gatewayDetails.peripherals = response.data.payload;
                    else
                        self.alertIT(response.data.message);
                    self.loading = false;
                })
                    .catch(function (error) {
                        self.parseErrors(error);
                        self.loading = false;
                    });
            },
            storeGateway(gatewayDetails) {
                if (!/\b((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.|$)){4}\b/.test(gatewayDetails.ipv4)) {
                    this.snackIT('The IPv4 address is not valid. Please check the data.');
                    return;
                }

                let self = this;
                this.loading = true;
                const data = {
                    _id: gatewayDetails._id,
                    ipv4: gatewayDetails.ipv4,
                    serial: gatewayDetails.serial,
                    name: gatewayDetails.name,
                };
                const url = gatewayDetails._id !== null ? '/updateGateway':'/storeGateway';
                axios.post(url, data).then(response => {
                    if (response.data.success) {
                        self.indexGateways();
                        self.snackIT('Changes stored successfully');
                        self.gatewayDetailsDialog = false;
                    }
                    else
                        self.alertIT(response.data.message);
                    self.loading = false;
                })
                    .catch(function (error) {
                        self.parseErrors(error);
                        self.loading = false;
                    });
            },
            destroyGateway(id) {
                let self = this;
                this.loading = true;
                axios.post('destroyGateway', {_id: id}).then(response => {
                    if (response.data.success) {
                        self.indexGateways();
                        self.snackIT('Gateway removed successfully');
                    }
                    else
                        self.alertIT(response.data.message);
                    self.loading = false;
                })
                    .catch(function (error) {
                        self.parseErrors(error);
                        self.loading = false;
                    });
            },
            storePeripheral(peripheralDetails) {
                let self = this;
                this.loading = true;
                const data = {
                    _id: peripheralDetails._id,
                    uid: peripheralDetails.uid,
                    vendor: peripheralDetails.vendor,
                    status: peripheralDetails.status,
                    id_gateway: peripheralDetails.id_gateway,
                };
                const url = peripheralDetails._id !== null ? '/updatePeripheral':'/storePeripheral';
                axios.post(url, data).then(response => {
                    if (response.data.success) {
                        self.indexGateways();
                        self.indexPeripherals(peripheralDetails.id_gateway);
                        self.snackIT('Changes stored successfully');
                        self.peripheralDetailsDialog = false;
                    }
                    else
                        self.alertIT(response.data.message);
                    self.loading = false;
                })
                    .catch(function (error) {
                        self.parseErrors(error);
                        self.loading = false;
                    });
            },
            destroyPeripheral(id, id_gateway) {
                let self = this;
                this.loading = true;
                axios.post('destroyPeripheral', {_id: id}).then(response => {
                    if (response.data.success) {
                        self.indexGateways();
                        self.indexPeripherals(id_gateway);
                        self.snackIT('Peripheral removed successfully');
                    }
                    else
                        self.alertIT(response.data.message);
                    self.loading = false;
                })
                    .catch(function (error) {
                        self.parseErrors(error);
                        self.loading = false;
                    });
            },
            parseErrors(error){
                const errors = error.response.data.errors;
                if (errors !== null && errors !== undefined) {
                    const arr = Object.values(errors);
                    let msg = '';
                    for (let i = 0; i < arr.length; i++)
                        msg += arr[i]+'<br>';
                    this.alertIT(msg);
                } else {
                    this.alertIT(error);
                    console.log(error);
                }
            },
        },
    }
</script>

<style scoped>
    >>> input:disabled {
        color: black;
    }

</style>