import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { taikhoan } from '../models/taikhoan';
import { category } from '../models/category';
import { bill } from '../models/bill';
import { food } from '../models/food';

@Injectable({
  providedIn: 'root'
})
export class DataService {
  private API = "http://localhost/project/QLNH_AGL_v1.1/api.php";
  private DATA_JSON = "http://localhost:3000";

  private httpOptions = {
    headers: new HttpHeaders({
      'Content-Type': 'application/json'
    })
  };
  constructor(private http: HttpClient) { }

  //kiểm tra tài khoản
  checkAccount(taikhoan: taikhoan): Observable<any> {
    const Username = taikhoan.Username;
    const Password = taikhoan.Password;
    const url = `${this.API}?action=checkAccount&Username=${Username}&Password=${Password}`;
    return this.http.get<any>(url);
  }

  //đọc file json 
  readAccountJson(taikhoan: taikhoan): Observable<any> {
    const url = `${this.DATA_JSON}/taikhoan`;
    return this.http.get<any>(url);
  }

  //lấy toàn bộ danh sách hóa đơn
  getTbBillHistory(): Observable<any> {
    const url = `${this.API}?action=home`;
    return this.http.get<any>(url);
  }

  //đọc tbBillHistory để tính sum
  readBillJson(): Observable<any> {
    const url = `${this.DATA_JSON}/totalAmount`;
    return this.http.get<any>(url);
  }

  //lấy toàn bộ bàn
  getTbDsTable(): Observable<any> {
    const url = `${this.API}?action=getTable`;
    return this.http.get<any>(url);
  }

  readTbDsTableJson() {
    const url = `${this.DATA_JSON}/ListTable`;
    return this.http.get<any>(url);
  }

  getTbDmFood(TableName: any): Observable<any> {
    const tablename = TableName;
    const url = `${this.API}?action=getDmFood&TableName=${tablename}`;
    return this.http.get<any>(url);
  }

  readTbDmFoodJson(): Observable<any> {
    const url = `${this.DATA_JSON}/ListCategory`;
    return this.http.get<any>(url);
  }

  getTbFood(DMFood: category): Observable<any> {
    const DMFoodID = DMFood.DMFoodID;
    const url = `${this.API}?action=getFood&DMFoodID=${DMFoodID}`;
    return this.http.get<any>(url);
  }

  readTbFoodJson(): Observable<any> {
    const url = `${this.DATA_JSON}/ListFood`;
    return this.http.get<any>(url);
  }

  addBill(FoodID: any, TableName: any, CustomerName: any, SDT: any): Observable<any> {
    const foodid = FoodID;
    const tablename = TableName;
    const BillDate = new Date();
    const customername = CustomerName;
    const sdt = SDT;

    // Lấy ngày, tháng, năm từ đối tượng Date
    const day = BillDate.getDate().toString().padStart(2, '0'); // Ngày
    const month = (BillDate.getMonth() + 1).toString().padStart(2, '0'); // Tháng (lưu ý +1 vì tháng bắt đầu từ 0)
    const year = BillDate.getFullYear(); // Năm

    // Chuỗi có định dạng yyyy/mm/dd
    const formattedDate = `${year}-${month}-${day}`;

    const url = `${this.API}?action=addBill&FoodID=${foodid}&TableName=${tablename}&BillDate=${formattedDate}&CustomerName=${customername}&SDT=${sdt}`;
    return this.http.get<any>(url);
  }

  plushBill(item: bill): Observable<any> {
    const foodName = item.FoodName;
    const tablename = item.TableName;
    const customername = item.CustomerName;
    const sdt = item.SDT;
    const url = `${this.API}?action=plushQuantityItem&FoodName=${foodName}&TableName=${tablename}&CustomerName=${customername}&SDT=${sdt}`;
    console.log(url)
    return this.http.get<any>(url);
  }

  getBillCurrentOfTable(TableName: any): Observable<any> {
    const tablename = TableName;
    const url = `${this.API}?action=getDatalBillCurrent&TableName=${tablename}`;
    return this.http.get<any>(url);
  }

  readBillCurrentOfTable(): Observable<any> {
    const url = `${this.DATA_JSON}/BillCurrentOfTable`;
    return this.http.get<any>(url);
  }
}
