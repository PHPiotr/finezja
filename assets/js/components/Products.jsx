import React, {Fragment} from 'react';

const Products = ({products, src}) => {
    if (!products || !products.length) {
        return null;
    }
    return (
        <div id="showcase">
            <div className="container">
                <div className="row">
                    <div className="price col-sm-12">
                        <div className="module_container">
                            <div className="mod-newsflash-adv mod-newsflash-adv__price cols-6" id="module_211">
                                <div className="row">
                                    {products.map(product => (
                                        <article className="col-sm-2 item visible-first">
                                            <figure className="item_img img-intro img-intro__">
                                                <img src={`${src}/${product.images[0].tile}`} alt={product.name}/>
                                                <figcaption>{`${(product.unitPrice / 100).toFixed(2)} zł`}</figcaption>
                                            </figure>
                                            <div className="item_content">
                                                <h6 className="item_title heading-style-6 visible-first">
                                                    {product.name}
                                                </h6>
                                                <div className="item_introtext">
                                                    <p>{product.description}</p>
                                                </div>
                                            </div>
                                            <div className="clearfix"></div>
                                        </article>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Products;
