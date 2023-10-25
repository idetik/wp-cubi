import del from 'del';

const patternsKnown = [];

export default (pattern, once = true) => {
    if (once && patternsKnown.includes(pattern)) {
        return;
    }
    patternsKnown.push(pattern);
    del.sync([pattern]);
};
