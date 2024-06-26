import { Component, OnInit } from '@angular/core';
import { DataService } from '../../service/data.service';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrl: './home.component.css'
})
export class HomeComponent implements OnInit {
  constructor(private service: DataService) { }

  totalAmount: number = 0;

  ngOnInit(): void {
    this.getTotalAmount();
  }

  getTotalAmount() {
    this.service.getTbBillHistory().subscribe(res => {
      this.service.readBillJson().subscribe(res=>{
        if(res && res.length > 0){
          res.forEach((element:any) => {
            this.totalAmount = element.TotalAmount;
          });
        }
      })
    })
  }
}
