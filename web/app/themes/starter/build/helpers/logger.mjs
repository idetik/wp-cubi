import chalk from 'chalk';

const { log } = console;
const info = chalk.bold.white;
const warning = chalk.keyword('orange');

const logger = (message, style = 'info') => {
    switch (style) {
    case 'info':
        log(info(message));
        break;
    case 'warning':
        log(warning(message));
        break;
    case 'taskTitle':
        log(`🍍 ${chalk.bold.underline.inverse.yellow(` ${message} `)}`);
        break;
    case 'none':
    default:
        log(message);
        break;
    }
};

export function loggerPlugin(entry = { message: '', type: 'info' }, hook = 'start') {
    // eslint-disable-next-line no-shadow
    const plug = (entry, parameters = []) => (
        entry instanceof Function ? entry(...parameters) : logger(entry.message, entry.type || 'info')
    );

    return {
        name: 'loggetik',
        options(options) {
            return hook === 'start' && plug(entry, [options]);
        },
        writeBundle(options, bundle) {
            return hook === 'end' && plug(entry, [options, bundle]);
        }
    };
}

export { logger };
