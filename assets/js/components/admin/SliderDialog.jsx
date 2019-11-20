import React, {useState} from 'react';
import {makeStyles} from '@material-ui/core/styles';
import Button from '@material-ui/core/Button';
import Dialog from '@material-ui/core/Dialog';
import AppBar from '@material-ui/core/AppBar';
import Toolbar from '@material-ui/core/Toolbar';
import IconButton from '@material-ui/core/IconButton';
import Typography from '@material-ui/core/Typography';
import CloseIcon from '@material-ui/icons/Close';
import Slide from '@material-ui/core/Slide';
import CardMedia from '@material-ui/core/CardMedia';
import Card from '@material-ui/core/Card';
import CloudUploadIcon from '@material-ui/icons/CloudUpload';
import ProgressIndicator from './ProgressIndicator';

const useStyles = makeStyles(theme => ({
    appBar: {
        position: 'relative',
    },
    title: {
        marginLeft: theme.spacing(2),
        flex: 1,
    },
    button: {
        margin: theme.spacing(1),
    },
    card: {
        maxWidth: '100%',
        marginBottom: theme.spacing(1),
    },
    media: {
        height: 140,
        backgroundSize: 'contain',
        backgroundPosition: 'left',
    },
}));

const Transition = React.forwardRef(function Transition(props, ref) {
    return <Slide direction="up" ref={ref} {...props} />;
});

const SliderDialog = props => {
    const classes = useStyles();
    const {open, onClose, title, onChange, slide, isProgress} = props;

    return (
        <Dialog fullScreen open={open} onClose={onClose} TransitionComponent={Transition}>
            <AppBar className={classes.appBar}>
                <Toolbar>
                    <IconButton edge="start" color="inherit" onClick={onClose} aria-label="close">
                        <CloseIcon/>
                    </IconButton>
                    <Typography variant="h6" className={classes.title}>
                        {title}
                    </Typography>
                </Toolbar>
            </AppBar>
            <Button
                component="label"
                variant="contained"
                color="default"
                className={classes.button}
                startIcon={<CloudUploadIcon/>}
            >
                {`${slide ? 'Zmień' : 'Dodaj'} zdjęcie slajdera`}
                <input
                    onChange={onChange}
                    style={{display: 'none'}}
                    value=""
                    type="file"
                />
            </Button>
            {slide && (
                <Card className={classes.card}>
                    <CardMedia
                        className={classes.media}
                        image={slide}
                    />
                </Card>
            )}
            {isProgress && <ProgressIndicator />}
        </Dialog>
    );
};

export default SliderDialog;
