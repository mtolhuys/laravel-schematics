export default {
    methods: {
        /**
         * @return {string}
         */
        uuid() {
            let date = new Date().getTime();

            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
                let random = (date + Math.random()*16)%16 | 0;

                date = Math.floor(date/16);

                return (c === 'x' ? random : (random&0x3|0x8)).toString(16);
            });
        }
    }
};
