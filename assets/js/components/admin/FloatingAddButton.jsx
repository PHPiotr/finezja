import React from 'react';
import Fab from '@material-ui/core/Fab';
import AddIcon from '@material-ui/icons/Add';
import { makeStyles, useTheme } from '@material-ui/core/styles';

const useStyles = makeStyles(theme => ({
    fab: {
        position: 'fixed',
        bottom: theme.spacing(2),
        right: theme.spacing(2),
    },
}));

const FloatingAddButton = props => {
    const classes = useStyles();
    return (
        <Fab href={props.href} aria-label={props.label} className={classes.fab} color={props.color}>
            <AddIcon />
        </Fab>
    );
};

FloatingAddButton.defaultProps = {
    color: 'primary',
    label: 'Dodaj',
};

export default FloatingAddButton;
