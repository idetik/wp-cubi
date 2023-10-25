import fs from 'fs';
import copy from 'rollup-plugin-copy';
import cleanPattern from './helpers/clean.mjs';
import { loggerPlugin } from './helpers/logger.mjs';

const destination = './dist/fonts';

export default [{
    input: './build/fonts.mjs',
    output: {
        file: './dist/fonts-dummy'
    },
    plugins: [
        loggerPlugin({
            message: `Build fonts into ${destination}`,
            type: 'taskTitle'
        }),
        {
            name: 'fontetik',
            buildStart() {
                cleanPattern('dist/fonts/*', true);
            },
            writeBundle(options) {
                fs.unlinkSync(options.file);
            }
        },
        copy({
            targets: [
                { src: 'assets/fonts/*', dest: destination }
            ]
        })
    ]
}];
