import React, { useEffect, useState } from "react";
import { getStatus, getProducts, insertCoin, purchase } from "../services/api";
import MoneyInput from "../components/MoneyInput";
import ProductCard from "../components/ProductCard";
import TransactionHistory from "../components/TransactionHistory";

const Home = () => {
  const [balance, setBalance] = useState(0);
  const [products, setProducts] = useState([]);
  const [history, setHistory] = useState([]);
  const [totalCash, setTotalCash] = useState(0);
  const [totalProducts, setTotalProducts] = useState(0);
  const [lastInsertedAmount, setLastInsertedAmount] = useState(0);
  const [message, setMessage] = useState("");
  const [log, setLog] = useState([]);

  const loadStatus = async () => {
    const res = await getStatus();
    setBalance(res.data.current_balance);
    setTotalCash(res.data.total_cash);
    setTotalProducts(res.data.total_products);
  };

  const loadProducts = async () => {
    const res = await getProducts();
    setProducts(res.data);
  };

  const handleInsertCoin = async (denomination) => {
    console.log("📥 เริ่มหยอดเงิน:", denomination);

    try {
      const res = await insertCoin({ denomination });
      console.log("✅ ตอบกลับจาก API insertCoin:", res.data);

      setBalance(res.data.current_balance);
      console.log("🧮 ตั้งค่า balance ใหม่:", res.data.current_balance);

      setLastInsertedAmount(denomination);
      // console.log("📌 ตั้งค่า lastInsertedAmount:", denomination);

      const logMsg = `เพิ่มเงิน ${denomination} บาทแล้ว และยอดคงเหลือที่ซื้อต่อได้ ${res.data.current_balance} บาท`;

      setHistory((prev) => {
        const newHistory = [logMsg, ...prev];
        console.log("📜 อัปเดต history:", newHistory);
        return newHistory;
      });

      setLog((prev) => {
        const newLog = [...prev, logMsg];
        console.log("🗂️ อัปเดต log:", newLog);
        return newLog;
      });

      await loadStatus();
      console.log("🔄 โหลดสถานะใหม่เรียบร้อย");
    } catch (error) {
      const errMsg = `❌ ${error.response?.data?.message || "เกิดข้อผิดพลาด"}`;
      setMessage(errMsg);
      setLog((prev) => {
        const newLog = [...prev, errMsg];
        console.log("⚠️ Error log:", newLog);
        return newLog;
      });
      console.error("🚨 เกิดข้อผิดพลาดในการหยอดเงิน:", error);
    }
  };

  const handlePurchase = async (productId) => {
    try {
      const res = await purchase({ product_id: productId });
      const data = res.data;

      console.log("Response จาก /purchase:", data);

      // อัปเดต balance จาก response ก่อนเลย
      setBalance(data.current_balance);
      console.log("Balance หลังอัปเดตจาก response:", data.current_balance);

      const changeAmount = data.change ?? 0;
      const changeDetails = data.change_details ?? {};

      const detailStrings = Object.entries(changeDetails)
        .filter(([_, qty]) => qty > 0)
        .map(([denom, qty]) => `${qty} x ${denom} บาท`);

      const detailLog = detailStrings.length
        ? ` (ทอนด้วย ${detailStrings.join(", ")})`
        : "";

      const successMsg = `✅ ซื้อ ${data.product.name} สำเร็จ! ทอน: ${changeAmount} บาท${detailLog}`;
      setMessage(successMsg);

      const logMsg = `ซื้อ ${data.product.name}, ทอน ${changeAmount} บาท${detailLog}`;
      setLog((prev) => [...prev, logMsg]);
      setHistory((prev) => [logMsg, ...prev]);

      await loadProducts();

      await loadStatus();
      console.log("Balance หลัง loadStatus:", balance);
    } catch (error) {
      const errMsg = `❌ ${error.response?.data?.message || "เกิดข้อผิดพลาด"}`;
      setMessage(errMsg);
      setLog((prev) => [...prev, errMsg]);
      console.error("Error ใน handlePurchase:", error);
    }
  };

  useEffect(() => {
    loadStatus();
    loadProducts();
  }, []);

  return (
    <div className="p-6 max-w-4xl mx-auto space-y-6">
      <h1 className="text-3xl font-bold text-center mb-6">
        🧃 Vending Machine
      </h1>

      {/* Summary Section */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
        <div className="bg-white shadow rounded-xl p-4">
          <p className="text-sm text-gray-500">ยอดเงินที่หยอดล่าสุด</p>
          <p className="text-xl font-semibold text-blue-600">
            {lastInsertedAmount} บาท
          </p>
        </div>
        <div className="bg-white shadow rounded-xl p-4">
          <p className="text-sm text-gray-500">ยอดคงเหลือที่ซื้อได้</p>
          <p className="text-xl font-semibold text-green-600">{balance} บาท</p>
        </div>
        <div className="bg-white shadow rounded-xl p-4">
          <p className="text-sm text-gray-500">
            สินค้า: {totalProducts} | เงินในเครื่อง: {totalCash} บาท
          </p>
        </div>
      </div>

      {/* Message */}
      {message && (
        <div className="bg-blue-50 border border-blue-200 text-blue-800 rounded p-3">
          {message}
        </div>
      )}

      {/* Insert Coin */}
      <div className="bg-white shadow rounded-xl p-4">
        <h2 className="text-lg font-semibold mb-2">💰 หยอดเงิน</h2>
        <MoneyInput onInsert={handleInsertCoin} />
      </div>

      {/* Product List */}
      <div>
        <h2 className="text-xl font-semibold mt-6 mb-4">🛒 รายการสินค้า</h2>
        <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
          {products.map((product) => (
            <ProductCard
              key={product.id}
              product={product}
              onBuy={handlePurchase}
              canBuy={product.price <= balance && product.stock > 0}
            />
          ))}
        </div>
      </div>

      {/* Transaction History */}
      <div className="bg-white shadow rounded-xl p-4">
        <h2 className="text-lg font-semibold mb-2">📜 ประวัติการทำรายการ</h2>
        <TransactionHistory history={history} />
      </div>
    </div>
  );
};

export default Home;
