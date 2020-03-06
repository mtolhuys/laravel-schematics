export default {
    methods: {
        config(key) {
            let value = null;

            key.split('.').forEach(key => value = value ? value[key] : Schematics.config[key]);

            return value;
        },

        uuid() {
            let date = new Date().getTime();

            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
                let random = (date + Math.random()*16)%16 | 0;

                date = Math.floor(date/16);

                return (c === 'x' ? random : (random&0x3|0x8)).toString(16);
            });
        },

        arrayDiff(a, b) {
            return [
                ...a.filter(x => b.indexOf(x) === -1),
                ...b.filter(x => a.indexOf(x) === -1)
            ];
        },

        arrayDiffByKey(key, ...arrays) {
            return [].concat(...arrays.map( (arr, i) => {
                const others = arrays.slice(0);

                others.splice(i, 1);

                const unique = [...new Set([].concat(...others))];

                return arr.filter( x =>
                    !unique.some(y => x[key] === y[key])
                );
            }));
        }
    }
};
