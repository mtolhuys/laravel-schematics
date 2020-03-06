export default {
    methods: {
        config(key) {
            let value = null;

            key.split('.').forEach(key => {
                if (! value && ! Schematics.config[key]) {
                    return null;
                }

                value = value ? value[key] : Schematics.config[key];
            });

            return value;
        },
    }
};
