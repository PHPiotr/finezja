import React from 'react';
import { makeStyles } from '@material-ui/core/styles';
import Box from '@material-ui/core/Box';
import CircularProgress from '@material-ui/core/CircularProgress';

const useStyles = makeStyles(theme => ({
    root: {
        position: 'absolute',
        display: 'flex',
        justifyContent: 'center',
        top: 0,
        left: 0,
        width: '100%',
        height: '100%',
        alignItems: 'center',
    },
}));

const ProgressIndicator = props => {
    const classes = useStyles();

    return (
        <Box className={classes.root}>
            <CircularProgress />
        </Box>
    );
};

export default ProgressIndicator;
