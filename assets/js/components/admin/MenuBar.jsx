import React, {useMemo, useState} from 'react';
import {makeStyles} from '@material-ui/core/styles';
import AppBar from '@material-ui/core/AppBar';
import Toolbar from '@material-ui/core/Toolbar';
import Typography from '@material-ui/core/Typography';
import Button from '@material-ui/core/Button';
import IconButton from '@material-ui/core/IconButton';
import MenuIcon from '@material-ui/icons/Menu';
import Drawer from '@material-ui/core/Drawer';
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemText from '@material-ui/core/ListItemText';
import {Link as RouterLink} from 'react-router-dom';
import Link from '@material-ui/core/Link';

const useStyles = makeStyles(theme => ({
    root: {
        flexGrow: 1,
    },
    menuButton: {
        marginRight: theme.spacing(2),
    },
    title: {
        flexGrow: 1,
    },
    list: {
        width: 250,
    },
}));

export default function ButtonAppBar() {
    const classes = useStyles();
    const [isDrawerOpen, setIsDrawerOpen] = useState(false);

    const toggleDrawer = () => setIsDrawerOpen(!isDrawerOpen);

    return (
        <div className={classes.root}>
            <AppBar position="static">
                <Toolbar>
                    <IconButton onClick={toggleDrawer} edge="start" className={classes.menuButton} color="inherit"
                                aria-label="menu">
                        <MenuIcon/>
                    </IconButton>
                    <Typography color="inherit" variant="h6" className={classes.title}>
                        Admin
                    </Typography>
                    <Button color="inherit" href="/logout">Wyloguj</Button>
                </Toolbar>
            </AppBar>
            <Drawer open={isDrawerOpen} onClose={toggleDrawer}>
                <div
                    className={classes.list}
                    role="presentation"
                >
                    <List>
                        {[{text: 'Oferta', href: '/admin/oferta'}].map(({text, href}) => (
                            <ListItem key={text} button component='a' href={href}>
                                <ListItemText primary={text} />
                            </ListItem>
                        ))}
                    </List>
                </div>
            </Drawer>
        </div>
    );
}
