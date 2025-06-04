import React from "react";

const TransactionHistory = ({ history }) => {
  if (history.length === 0) return null;

  return (
    <div className="mt-8">
      <h2 className="text-lg font-semibold mb-2">ประวัติรายการ</h2>
      <ul className="bg-gray-50 border rounded-xl p-4 text-sm space-y-1 max-h-40 overflow-y-auto">
        {history.map((item, index) => (
          <li key={index} className="text-gray-700">
            • {item}
          </li>
        ))}
      </ul>
    </div>
  );
};

export default TransactionHistory;
