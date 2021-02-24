export default {
    prepareValue(value){
        if(value) {
            let formData = new FormData();
            for (const elem in value) {
                formData.append(value[elem].id, value[elem].value);
            }
            return formData;
        }
        return null;
    },
}
