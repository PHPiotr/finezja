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
import DragHandleIcon from "@material-ui/icons/DragHandle";
import CardActions from '@material-ui/core/CardActions';
import IconButton from '@material-ui/core/IconButton';
import {SortableContainer, SortableElement, SortableHandle} from 'react-sortable-hoc';
import arrayMove from 'array-move';
import MessageBar from './MessageBar';
import ProgressIndicator from './ProgressIndicator';

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

const DragHandle = SortableHandle(() => (
    <IconButton>
        <DragHandleIcon/>
    </IconButton>
));

const SortableItem = SortableElement(({
    classes,
    name,
    url,
    image,
    imageDescriptions,
    handleSetImage,
    handleSetImageDescriptions,
    handleRemoveFile,
}) => (
    <Card className={classes.card}>
        <CardMedia
            className={classes.media}
            image={url}
        />
        <CardActions disableSpacing>
            <DragHandle/>
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
                <DeleteIcon color="secondary"/>
            </IconButton>
            <TextField
                style={{margin: 8}}
                placeholder="Opis"
                margin="normal"
                defaultValue={imageDescriptions[name]}
                fullWidth
                onChange={handleSetImageDescriptions(name)}
                InputLabelProps={{
                    shrink: true,
                }}
            />
        </CardActions>
    </Card>
));

const SortableListContainer = SortableContainer(({
    classes,
    image,
    imageDescriptions,
    handleSetImage,
    handleSetImageDescriptions,
    handleRemoveFile,
    fileNames,
    filesByName,
}) => (
    <Box>
        {fileNames.filter(fileName => {
            const {name, url} = filesByName[fileName] || {};
            return name && url;
        }).map((fileName, index) => {
            const {name, url} = filesByName[fileName];
            return (
                <SortableItem
                    key={name}
                    classes={classes}
                    name={name}
                    url={url}
                    image={image}
                    imageDescriptions={imageDescriptions}
                    handleSetImage={handleSetImage}
                    handleSetImageDescriptions={handleSetImageDescriptions}
                    handleRemoveFile={handleRemoveFile}
                    index={index}
                    idx={index}
                />
            );
        })}
    </Box>
));

const NewCategory = props => {
    const [category, setCategory] = useState({});
    const [isEditMode, setIsEditMode] = useState(!!props.categoryId);
    const classes = useStyles();
    const [categoryName, setCategoryName] = useState('');
    const [shortDescription, setShortDescription] = useState('');
    const [longDescription, setLongDescription] = useState('');
    const [image, setImage] = useState('');
    const [imagesToRemove, setImagesToRemove] = useState([]);
    const [isProgress, setIsProgress] = useState(false);
    const [imageDescriptions, setImageDescriptions] = useState({});

    // snackbar state
    const [message, setMessage] = useState('');
    const [variant, setVariant] = useState('info');
    const [open, setOpen] = useState(false);

    const handleClose = () => setOpen(!open);

    const [fileNames, setFileNames] = useState([]);
    const [filesByName, setFilesByName] = useState({});

    useEffect(async () => {
        if (!isEditMode) {
            return;
        }
        try {
            setIsProgress(true);
            const response = await axios(`/admin/categories/${categoryId}`);
            if (response.data && response.data.category) {
                setCategory(response.data.category);
                setCategoryName(response.data.category.name);
                setShortDescription(response.data.category.shortDescription);
                setLongDescription(response.data.category.longDescription);
                setImage(response.data.category.image);
            }
        } catch(e) {
            setMessage(e.message || 'Something went wrong...');
            setVariant(error);
            setOpen(true);
        } finally {
            setIsProgress(false);
        }
    }, [categoryId]);

    useEffect(() => {
        if (!isEditMode) {
            return;
        }
        if (!category) {
            return;
        }
        targetFileNames = (category.images || []).map(i => i.name);
        targetFilesByName = (category.images || []).reduce((acc, curr) => {
            acc[curr.name] = {
                file: null,
                name: curr.name,
                url: curr.name,
            };
            return acc;
        }, {});
        const newImageDescriptions = (category.images || []).reduce((acc, curr) => {
            acc[curr.name] = curr.description;
            return acc;
        }, {});
        setImageDescriptions(newImageDescriptions);
        setFileNames(targetFileNames);
        setFilesByName(targetFilesByName);
    }, [category]);

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
    const handleSetImageDescriptions = name => e => setImageDescriptions({...imageDescriptions, [name]: e.target.value});
    const handleCategoryNameInputChange = e => setCategoryName(e.target.value);
    const handleShortDescriptionInputChange = e => setShortDescription(e.target.value);
    const handleLongDescriptionInputChange = e => setLongDescription(e.target.value);
    const handleCreateCategory = async () => {
        setIsProgress(true);
        const data = new FormData();
        data.append('name', categoryName);
        data.append('image', image);
        data.append('shortDescription', shortDescription);
        data.append('longDescription', longDescription);

        fileNames.forEach((fileName, i) => {
            data.append(`imageDescriptions[]`, imageDescriptions[fileName] || '');
            data.append(`fileNames[]`, fileName);
            const {file} = filesByName[fileName];
            if (file) {
                data.append(`images_${i}`, file);
            }
        });

        if (isEditMode) {
            imagesToRemove.forEach(imageToRemove => {
                data.append('imagesToRemove[]', imageToRemove);
            });
        }

        try {
            const response = await axios(isEditMode ? `/admin/categories/${category.id}` : `/admin/categories/add`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
                data,
            });

            if (isEditMode) {
                setCategory(response.data.category);
                setOpen(true);
                setMessage('Kategoria zmieniona');
                setVariant('success');
                setImagesToRemove([]);
            } else {
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
            }
        } catch (e) {
            setOpen(true);
            setMessage(e.message);
            setVariant('error');
        } finally {
            setIsProgress(false);
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
        if (isEditMode && (category.images || []).find(i => i.name === name)) {
            setImagesToRemove([...imagesToRemove, name]);
        }
    };
    const onSortEnd = sort => {
        const {oldIndex, newIndex} = sort;
        if (oldIndex === newIndex) {
            return;
        }
        const newFileNames = arrayMove(fileNames, oldIndex, newIndex);
        targetFileNames = [...newFileNames];
        setFileNames(newFileNames);
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
                {fileNames.length > 0 && (
                    <SortableListContainer
                        classes={classes}
                        image={image}
                        imageDescriptions={imageDescriptions}
                        handleSetImage={handleSetImage}
                        handleSetImageDescriptions={handleSetImageDescriptions}
                        handleRemoveFile={handleRemoveFile}
                        fileNames={fileNames}
                        filesByName={filesByName}
                        useDragHandle={true}
                        lockAxis="y"
                        onSortEnd={onSortEnd}
                    />
                )}
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
                    >{`${isEditMode ? 'Edytuj' : 'Utwórz'} kategorię oferty`}</Button>
                </Box>
                <MessageBar open={open} message={message} variant={variant} handleClose={handleClose}/>
            </Container>
            {isProgress && <ProgressIndicator />}
        </Fragment>
    );
};

export default NewCategory;
