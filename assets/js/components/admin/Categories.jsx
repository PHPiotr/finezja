import React, {Fragment, useState, useEffect} from 'react';
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemText from '@material-ui/core/ListItemText';
import ListItemAvatar from '@material-ui/core/ListItemAvatar';
import Avatar from '@material-ui/core/Avatar';
import DragHandleIcon from "@material-ui/icons/DragHandle";
import DeleteIcon from "@material-ui/icons/Delete";
import SliderIcon from "@material-ui/icons/PhotoCamera";
import axios from 'axios';
import {SortableContainer, SortableElement, SortableHandle} from 'react-sortable-hoc';
import arrayMove from 'array-move';
import ListItemIcon from '@material-ui/core/ListItemIcon';
import FloatingAddButton from './FloatingAddButton';
import ConfirmationDialog from './ConfirmationDialog';
import MessageBar from './MessageBar';
import ProgressIndicator from './ProgressIndicator';
import SliderDialog from './SliderDialog';

const DragHandle = SortableHandle(() => (
    <ListItemIcon>
        <DragHandleIcon/>
    </ListItemIcon>
));

const SortableItem = SortableElement(({id, idx, text, image, slide, onDeleteClick, onSliderClick}) => {
    return (
        <ListItem button component="a" href={`/admin/oferta/${id}`}>
            <DragHandle/>
            <ListItemIcon onClick={e => {
                e.preventDefault();
                onDeleteClick({id, idx, text});
            }}>
                <DeleteIcon/>
            </ListItemIcon>
            <ListItemIcon onClick={e => {
                e.preventDefault();
                onSliderClick({id, idx, text, slide});
            }}>
                <SliderIcon/>
            </ListItemIcon>
            <ListItemAvatar>
                <Avatar alt={text} src={image}/>
            </ListItemAvatar>
            <ListItemText primary={text}/>
        </ListItem>
    )
});

const SortableListContainer = SortableContainer(({items, onDeleteClick, onSliderClick}) => (
    <List component="div">
        {items.map(({id, name, image, slide}, index) => (
            <SortableItem
                key={id}
                index={index}
                idx={index}
                text={name}
                image={image}
                id={id}
                onDeleteClick={onDeleteClick}
                onSliderClick={onSliderClick}
                slide={slide || ''}
            />
        ))}
    </List>
));

const Categories = props => {
    const [items, setItems] = useState([]);
    const [deleteCategoryDialogOpen, setDeleteCategoryDialogOpen] = useState(false);
    const [sliderDialogOpen, setSliderDialogOpen] = useState(false);
    const [slideId, setSlideId] = useState(null);
    const [slide, setSlide] = useState('');
    const [sliderDialogTitle, setSliderDialogTitle] = useState('');
    const [slideUrl, setSlideUrl] = useState('');
    const [deleteCategoryDialogDescription, setDeleteCategoryDialogDescription] = useState('');
    const [deleteCategory, setDeleteCategory] = useState(null);
    const [deleteCategoryIndex, setDeleteCategoryIndex] = useState(null);
    const [isProgress, setIsProgress] = useState(false);
    const [message, setMessage] = useState('');
    const [variant, setVariant] = useState('info');
    const [open, setOpen] = useState(false);

    const handleClose = () => setOpen(!open);

    useEffect(() => {
        (async () => {
            try {
                setIsProgress(true);
                const response = await axios(`/admin/categories`);
                if (response.data && response.data.categories) {
                    setItems(response.data.categories)
                }
            } catch (e) {
                setMessage(e.message || 'Something went wrong...');
                setVariant('error');
                setOpen(true);
            } finally {
                setIsProgress(false);
            }
        })();
    }, []);

    const onSortEnd = async (sort) => {
        setIsProgress(true);
        const {oldIndex, newIndex} = sort;
        if (oldIndex === newIndex) {
            return;
        }
        const newItems = arrayMove(items, oldIndex, newIndex);
        setItems(newItems);
        try {
            await axios({
                method: 'put',
                url: '/admin/categories/sort',
                data: {
                    oldIndex,
                    newIndex,
                    slug: newItems[newIndex].slug,
                }
            });
        } catch (e) {
            setMessage(e.message || 'Something went wrong...');
            setVariant('error');
            setOpen(true);
            const revertedItems = arrayMove(items, newIndex, oldIndex);
            setItems(revertedItems);
        } finally {
            setIsProgress(false);
        }
    };

    const handleOnDeleteCategoryClick = ({id, idx, text}) => {
        setDeleteCategoryDialogOpen(true);
        setDeleteCategoryDialogDescription(`Usunąć ${text}?`);
        setDeleteCategory(items[idx]);
        setDeleteCategoryIndex(idx);
    };

    const handleOnSliderClick = ({id, idx, text, slide}) => {
        setSliderDialogTitle(text);
        setSliderDialogOpen(true);
        setSlideId(id);
        setSlide(slide);
    };

    const handleDeleteCategoryConfirm = async () => {
        setIsProgress(true);
        setDeleteCategoryDialogOpen(false);
        setDeleteCategoryDialogDescription('');
        const newItems = items.filter(i => i.id !== deleteCategory.id);
        setItems(newItems);
        try {
            await axios({
                method: 'delete',
                url: `/admin/categories/${deleteCategory.id}`,
            });
        } catch (e) {
            setMessage(e.message || 'Something went wrong...');
            setVariant('error');
            setOpen(true);
            const revertedItems = [...newItems];
            revertedItems.splice(deleteCategoryIndex, 0, deleteCategory);
            setItems(revertedItems);
        } finally {
            setDeleteCategory(null);
            setDeleteCategoryIndex(null);
            setIsProgress(false);
        }
    };
    const handleDeleteCategoryCancel = () => {
        setDeleteCategoryDialogOpen(false);
        setDeleteCategoryDialogDescription('')
    };

    const handleCloseSliderDialog = () => {
        setSliderDialogOpen(false);
        setSliderDialogTitle('');
    };

    const handleChangeSlider = async e => {
        const oldSlide = slide;
        setIsProgress(true);
        setSlide('');
        const data = new FormData();
        const newSlide = Array.from(e.target.files)[0];
        data.append(`slide`, newSlide);
        try {
            const response = await axios(`/admin/categories/${slideId}/slider`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
                data,
            });
            if (response.data.slide) {
                setSlide(response.data.slide);
                setItems(items.map(i => {
                    if (i.id === slideId) {
                        return {...i, slide: response.data.slide}
                    }
                    return i;
                }))
            }
        } catch(e) {
            setSlide(oldSlide);
            setOpen(true);
            setVariant('error');
            setMessage(e.message);
        } finally {
            setIsProgress(false);
        }
    };

    return (
        <Fragment>
            <SortableListContainer
                items={items}
                onDeleteClick={handleOnDeleteCategoryClick}
                onSliderClick={handleOnSliderClick}
                onSortEnd={onSortEnd}
                useDragHandle={true}
                lockAxis="y"
            />
            <FloatingAddButton href="/admin/oferta/nowa-kategoria"/>
            <ConfirmationDialog
                open={deleteCategoryDialogOpen}
                title="Usuwanie kategorii"
                description={deleteCategoryDialogDescription}
                onConfirm={handleDeleteCategoryConfirm}
                onCancel={handleDeleteCategoryCancel}
                onClose={handleDeleteCategoryCancel}
            />
            <SliderDialog
                open={sliderDialogOpen}
                onClose={handleCloseSliderDialog}
                title={sliderDialogTitle}
                url={slideUrl}
                onChange={handleChangeSlider}
                slide={slide}
                isProgress={isProgress}
            />
            <MessageBar open={open} message={message} variant={variant} handleClose={handleClose}/>
            {isProgress && <ProgressIndicator />}
        </Fragment>
    );
};

export default Categories;
