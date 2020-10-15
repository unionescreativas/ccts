<template>
    <div>
        <VueFileAgent
            ref="vueFileAgent"
            :theme="'list'"
            :multiple="true"
            :deletable="true"
            :meta="true"
            :accept="'.pdf'"
            :maxSize="'2048MB'"
            :maxFiles="1"
            :helpText="'Cargue el documento en formato pdf aquí'"
            :errorText="{
                type: 'Documento invalido solo puedes cargar archivos Pdf',
                size: 'Files should not exceed 10MB in size'
            }"
            @select="filesSelected($event)"
            @beforedelete="onBeforeDelete($event)"
            @delete="fileDeleted($event)"
            v-model="fileRecords"
        ></VueFileAgent>
        <button :disabled="!fileRecordsForUpload.length" @click="uploadFiles()">
            Cargado {{ fileRecordsForUpload.length }} Archivo
        </button>
    </div>
</template>

<script>
import axios from "axios";
export default {
    data: function() {
        return {
            fileInEdit: null,
            fileRecords: [],
            fileRecordsForUpload: []
        };
    },
    methods: {
        async consultarDocumentos() {
            try {
                let res = await axios.get(`/documentos`);
                let documentos = res.data.data.map(data => ({
                    ...data,
                    name: data.nombre_carga,
                    lastModified: data.updated_at,
                    size: data.tamano,
                    type: data.aplicacion,
                    url: `${process.env.API_URL}/storage${data.url_descarga}`
                }));
                console.log(res);
                this.fileRecords = documentos;
            } catch (err) {}
        },
        uploadFiles: function() {
            console.log("uploadFiles");
            let formData = new FormData();
            let mensajeGuardar = "El documento se ha subido con éxito!";
            let mensajeError =
                "No se pudo subir el documento, consulte con el administrador!";
            this.fileRecordsForUpload.forEach(({ file }, index) =>
                formData.append(`file[${index}]`, file, file.newName)
            );
            // Using the default uploader. You may use another uploader instead.
            console.log(formData);
            try {
                let res = axios.post("/documentos", formData, {
                    header: { "Content-type": "multipart/form-data" }
                });
                this.fileRecordsForUpload = [];
                this.$swal
                    .fire({
                        html: `<h4>${mensajeGuardar}</h4>`,
                        icon: "success",
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    })
                    .then(result => {
                        if (result.value) {
                            payload.fileRecordsForUpload = [];
                        }
                    });
            } catch (err) {
                console.log(err);
                let status = 500;
                // ERROR DEL CLIENTE
                if (status <= 499) {
                    // ERROR DEL SERVIDOR
                } else {
                    this.$swal.fire({
                        html: `<h4>${mensajeError}</h4>`,
                        icon: "error",
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                }
            }
        },
        deleteUploadedFile: function(fileRecord) {
            // Using the default uploader. You may use another uploader instead.
            this.$refs.vueFileAgent.deleteUpload(
                this.uploadUrl,
                this.uploadHeaders,
                fileRecord
            );
        },
        filesSelected: function(fileRecordsNewlySelected) {
            console.log("filesSelected");
            var validFileRecords = fileRecordsNewlySelected.filter(
                fileRecord => !fileRecord.error
            );
            this.fileRecordsForUpload = this.fileRecordsForUpload.concat(
                validFileRecords
            );
            this.uploadFiles();
        },
        onBeforeDelete: function(fileRecord) {
            var i = this.fileRecordsForUpload.indexOf(fileRecord);
            if (i !== -1) {
                this.fileRecordsForUpload.splice(i, 1);
            } else {
                if (confirm("Are you sure you want to delete?")) {
                    this.$refs.vueFileAgent.deleteFileRecord(fileRecord); // will trigger 'delete' event
                }
            }
        },
        fileDeleted: function(fileRecord) {
            var i = this.fileRecordsForUpload.indexOf(fileRecord);
            if (i !== -1) {
                this.fileRecordsForUpload.splice(i, 1);
            } else {
                this.deleteUploadedFile(fileRecord);
            }
        }
    },
    created() {
        this.consultarDocumentos(this);
    }
};
</script>
<style></style>
