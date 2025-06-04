import React from "react";

const ProductCard = ({ product, onBuy, canBuy }) => {
  return (
    <div className="border rounded-2xl shadow-md p-4 flex flex-col justify-between bg-white">
      <div>
        <h3 className="text-lg font-bold">{product.name}</h3>
        <p className="text-sm text-gray-600 mb-1">ราคา: {product.price} บาท</p>
        <p className="text-sm text-gray-500">คงเหลือ: {product.stock} ชิ้น</p>
      </div>
      <button
        disabled={!canBuy}
        onClick={() => onBuy(product.id)}
        className={`mt-3 py-2 px-4 rounded-xl font-semibold text-white ${
          canBuy
            ? "bg-green-500 hover:bg-green-600"
            : "bg-gray-400 cursor-not-allowed"
        }`}
      >
        ซื้อ
      </button>
    </div>
  );
};

export default ProductCard;
