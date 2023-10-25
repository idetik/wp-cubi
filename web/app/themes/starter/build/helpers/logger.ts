interface loggerInterface {
    message: string,
    style: 'info' | 'warning' | 'taskTitle' | 'none'
}

declare function logger(message: string, style: 'info' | 'warning' | 'taskTitle' | 'none');
declare function loggerPlugin(loggerInterface, hook: string);

