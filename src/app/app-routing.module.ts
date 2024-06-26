import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { HomeComponent } from './components/home/home.component';
import { LoginComponent } from './components/login/login.component';
import { GoimonComponent } from './components/goimon/goimon.component';
import { GiaodiengoimonComponent } from './components/giaodiengoimon/giaodiengoimon.component';

const routes: Routes = [
  { path: '', redirectTo: '/login', pathMatch: "full" },
  { path: 'login', component: LoginComponent },
  { path:'home', component:HomeComponent},
  { path:'goimon', component:GoimonComponent},
  { path:'giaodiengoimon', component:GiaodiengoimonComponent}
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
