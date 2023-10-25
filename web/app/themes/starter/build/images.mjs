import imagemin from 'imagemin';
import imageminJpegtran from 'imagemin-jpegtran';
import fs from 'fs';
import path from 'path';
import glob from 'glob';
import cleanPattern from './helpers/clean.mjs';
import { loggerPlugin } from './helpers/logger.mjs';

const destination = './dist/images';
const ticksymbol = process.env.SHELL.indexOf('bash') !== -1 ? '✔' : '√';

export default [{
    input: './build/images.mjs',
    output: {
        dir: destination
    },
    plugins: [
        loggerPlugin({
            message: `Build images into ${destination}`,
            type: 'taskTitle'
        }),
        {
            name: 'imagetik',
            buildStart() {
                cleanPattern('dist/images/*', true);
            },
            writeBundle(options) {
                fs.unlinkSync(`${options.dir}/images.js`);
                const files = glob.sync('assets/images/**/*.{jpg,jpeg,png,svg,ico,mp4,webp}');
                files.forEach((file) => {
                    const output = path.dirname(file.replace('assets/images', destination));
                    (async () => {
                        await imagemin([file], {
                            destination: output,
                            plugins: [
                                imageminJpegtran()
                            ]
                        }).then(() => {
                            // eslint-disable-next-line no-console
                            console.log('\x1b[32m%s\x1b[0m', ticksymbol, `${output}/${path.basename(file)}`);
                        });
                    })();
                });
            }
        }
    ]
}];
