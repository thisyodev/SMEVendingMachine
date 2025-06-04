import React from "react";

const denominations = [1, 5, 10, 20, 50, 100, 500, 1000];

const MoneyInput = ({ onInsert }) => {
  return (
    <div className="mb-6">
      <h2 className="text-lg font-medium mb-2">หยอดเหรียญ/ธนบัตร</h2>
      <div className="grid grid-cols-4 gap-2">
        {denominations.map((denom) => (
          <button
            key={denom}
            className="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-3 rounded-xl shadow"
            onClick={() => onInsert(denom)}
          >
            {denom} บาท
          </button>
        ))}
      </div>
    </div>
  );
};

export default MoneyInput;
