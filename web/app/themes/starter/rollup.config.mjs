import stylesTasks from './build/styles.mjs';
import scriptsTasks from './build/scripts.mjs';
import fontsTasks from './build/fonts.mjs';
import imagesTasks from './build/images.mjs';

const TASKS = [
    'js',
    'css',
    'images',
    'fonts'
];
const tasksRequest = (process.env.tasks && process.env.tasks.split('-')) || false;
let run = TASKS;

if (!process.env.NODE_ENV) {
    process.env.NODE_ENV = 'development';
}

if (tasksRequest) {
    run = run.filter((x) => tasksRequest.includes(x));
}
const tasks = [];

if (run.includes('css') && stylesTasks.length > 0) {
    tasks.push(...stylesTasks);
}

if (run.includes('js') && scriptsTasks.length > 0) {
    tasks.push(...scriptsTasks);
}

if (run.includes('fonts') && fontsTasks.length > 0) {
    tasks.push(...fontsTasks);
}

if (run.includes('images') && imagesTasks.length > 0) {
    tasks.push(...imagesTasks);
}

export default tasks;
