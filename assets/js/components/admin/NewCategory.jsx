import React, {Fragment, useState, useEffect} from 'react';
import axios from 'axios';
import {makeStyles} from '@material-ui/core/styles';
import CssBaseline from '@material-ui/core/CssBaseline';
import TextField from '@material-ui/core/TextField';
import Container from '@material-ui/core/Container';
import Button from '@material-ui/core/Button';
import CloudUploadIcon from '@material-ui/icons/CloudUpload';
import CardMedia from '@material-ui/core/CardMedia';
import Card from '@material-ui/core/Card';
import Box from '@material-ui/core/Box';
import FavoriteIcon from '@material-ui/icons/Favorite';
import DeleteIcon from '@material-ui/icons/Delete';
import CardActions from '@material-ui/core/CardActions';
import IconButton from '@material-ui/core/IconButton';
import MessageBar from './MessageBar';

const useStyles = makeStyles(theme => ({
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

let targetFileNames = [];
let targetFilesByName = {};

const NewCategory = props => {
    const classes = useStyles();
    const [categoryName, setCategoryName] = useState('');
    const [shortDescription, setShortDescription] = useState('');
    const [longDescription, setLongDescription] = useState('');
    const [image, setImage] = useState('');
    const [message, setMessage] = useState('');
    const [variant, setVariant] = useState('info');
    const [open, setOpen] = useState(false);

    const handleClose = () => setOpen(!open);

    const [fileNames, setFileNames] = useState([]);
    const [filesByName, setFilesByName] = useState({});

    const handleFileInputChange = e => {
        const newFiles = Array.from(e.target.files);
        newFiles.forEach(file => {
            const reader = new FileReader();
            reader.onload = ({target}) => {
                const url = target.result;
                const {name} = file;
                if (fileNames.indexOf(name) === -1 && (fileNames.map(i => (filesByName[i] || {}).url)).indexOf(url) === -1) {
                    targetFilesByName[name] = {file, name, url};
                    targetFileNames.push(name);
                    setFilesByName({...targetFilesByName});
                    setFileNames([...targetFileNames]);
                }
            };
            reader.readAsDataURL(file);
        });
    };
    const handleSetImage = name => () => setImage(name);
    const handleCategoryNameInputChange = e => setCategoryName(e.target.value);
    const handleShortDescriptionInputChange = e => setShortDescription(e.target.value);
    const handleLongDescriptionInputChange = e => setLongDescription(e.target.value);
    const handleCreateCategory = async () => {
        const data = new FormData();
        data.append('name', categoryName);
        data.append('image', image);
        data.append('shortDescription', shortDescription);
        data.append('longDescription', longDescription);

        fileNames.forEach((fileName, i) => {
            data.append(`images_${i}`, filesByName[fileName].file);
        });

        try {
            const result = await axios(`/admin/categories/add`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
                data,
            });

            targetFileNames = [];
            targetFilesByName = {};

            setCategoryName('');
            setShortDescription('');
            setLongDescription('');
            setImage('');
            setFileNames([]);
            setFilesByName({});

            setOpen(true);
            setMessage('Kategoria dodana');
            setVariant('success');
        } catch (e) {
            setOpen(true);
            setMessage(e.message);
            setVariant('error');
        }
    };
    const handleRemoveFile = name => () => {
        targetFileNames = targetFileNames.filter(fileName => fileName !== name);
        delete targetFilesByName[name];
        if (name === image) {
            setImage('');
        }
        setFileNames(fileNames.filter(fileName => fileName !== name));
        setFilesByName({...filesByName, [name]: undefined});
    };

    return (
        <Fragment>
            <CssBaseline/>
            <Container>
                <TextField
                    id="name"
                    style={{margin: 8}}
                    placeholder="Nazwa kategorii"
                    fullWidth
                    margin="normal"
                    value={categoryName}
                    onChange={handleCategoryNameInputChange}
                    InputLabelProps={{
                        shrink: true,
                    }}
                />
                <TextField
                    style={{margin: 8}}
                    fullWidth
                    multiline
                    margin="normal"
                    value={shortDescription}
                    onChange={handleShortDescriptionInputChange}
                    rows={3}
                    placeholder="Krótki opis"
                    InputLabelProps={{
                        shrink: true,
                    }}
                />
                <TextField
                    style={{margin: 8}}
                    fullWidth
                    multiline
                    margin="normal"
                    value={longDescription}
                    onChange={handleLongDescriptionInputChange}
                    rows={5}
                    placeholder="Długi opis"
                    InputLabelProps={{
                        shrink: true,
                    }}
                />
                {fileNames.length > 0 && fileNames.map(fileName => {
                    const {name, url} = filesByName[fileName] || {};
                    if (!name || !url) {
                        return;
                    }
                    return (
                        <Card className={classes.card} key={name}>
                            <CardMedia
                                className={classes.media}
                                image={url}
                            />
                            <CardActions disableSpacing>
                                <IconButton
                                    aria-label="Dodaj jako główne"
                                    onClick={handleSetImage(name)}
                                    disabled={name === image}
                                >
                                    <FavoriteIcon color={name === image ? `primary` : `disabled`}/>
                                </IconButton>
                                <IconButton
                                    aria-label="Usuń zdjęcie"
                                    onClick={handleRemoveFile(name)}
                                >
                                    <DeleteIcon color="secondary" />
                                </IconButton>
                            </CardActions>
                        </Card>
                    )
                })}
                <Button
                    component="label"
                    variant="contained"
                    color="default"
                    className={classes.button}
                    startIcon={<CloudUploadIcon/>}
                >
                    Dodaj zdjęcia
                    <input
                        onChange={handleFileInputChange}
                        style={{display: 'none'}}
                        value=""
                        type="file"
                        multiple
                    />
                </Button>
                <Box>
                    <Button
                        variant="contained"
                        color="primary"
                        className={classes.button}
                        disabled={!categoryName || fileNames.length === 0 || !image}
                        onClick={handleCreateCategory}
                    >Utwórz kategorię oferty</Button>
                </Box>
                <MessageBar open={open} message={message} variant={variant} handleClose={handleClose} />
            </Container>
        </Fragment>
    );
};

export default NewCategory;
