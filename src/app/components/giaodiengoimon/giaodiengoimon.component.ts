import { Component, OnInit } from '@angular/core';
import { DataService } from '../../service/data.service';
import { ActivatedRoute, Router } from '@angular/router';
import { food } from '../../models/food';
import { category } from '../../models/category';
import { bill } from '../../models/bill';

@Component({
  selector: 'app-giaodiengoimon',
  templateUrl: './giaodiengoimon.component.html',
  styleUrl: './giaodiengoimon.component.css'
})
export class GiaodiengoimonComponent implements OnInit {
  TableName: any;
  nameUser: string = "";
  accType: string = "";
  listDmFood: any;
  listFood: any;
  listItemBill: any;
  bill: bill = <bill>{};
  thongbao: string = "";
  foodid: any;

  constructor(private server: DataService, private route: ActivatedRoute, private router: Router) { }

  ngOnInit(): void {
    this.route.queryParams.subscribe(params => {
      this.nameUser = params['Username'];
      this.accType = params['AccountType'];
      this.TableName = params['TableName'];
    });

    this.listFood = [];
    this.listDmFood = [];
    this.listItemBill = [];
    
    this.getTbDmFood();

  }

  getTbDmFood() {
    this.server.getTbDmFood(this.TableName).subscribe(res => {
      this.server.readTbDmFoodJson().subscribe((res) => {
        this.listDmFood = res;
        this.getListFood();
        this.readCurrentBill();
      })
    });
  }

  getListFood(){
    this.server.readTbFoodJson().subscribe(res => {
      this.listFood = res;
    })
  }

  getFood(DMFoodID: any) {
    this.server.getTbFood(DMFoodID).subscribe(res => {
      this.getListFood();
    });
    // Đặt isSelected = false cho tất cả các mục
    this.listDmFood.forEach((element: any) => {
      element.isSelected = false;
    });
  
    // Đặt isSelected = true cho mục được chọn
    DMFoodID.isSelected = true;
  }
  

  addBill(itemFood: any) {
    if(this.bill.CustomerName == null || this.bill.SDT == null){
      this.thongbao = "Hãy nhập đầy đủ thông tin khách hàng!!!";
      return;
    }

    this.thongbao = "";
    this.server.addBill(itemFood.FoodID, this.TableName, this.bill.CustomerName, this.bill.SDT).subscribe(() => {
      //lấy dữ liệu
      this.getCurrentBill();
    });
  }

  getCurrentBill(){
    this.server.getBillCurrentOfTable(this.TableName).subscribe((res)=>{
      //show dữ liệu
      this.readCurrentBill();
    });
  }

  readCurrentBill() {
    this.server.readBillCurrentOfTable().subscribe(res => {
      res.forEach((element:any) => {
        this.bill.CustomerName = element.CustomerName;
        this.bill.SDT = element.SDT;
        this.listItemBill = res || [];
      });
    });
  }

  plushItem(item: any){
    this.server.plushBill(item).subscribe(()=>{
      //lấy dữ liệu
      this.getCurrentBill();
      //show dữ liệu
      this.readCurrentBill();
    });
  }
}
