import React from 'react/addons';
import { Router, Route, IndexRoute, Link } from 'react-router'
import Backbone from 'backbone'
import history from './history'

import {
	MasterLedgerHandler,
	EconomyOverviewHandler,
} from './Economy/Other'

import {
	EconomyAccountingInstructionsHandler,
	EconomyAccountingInstructionHandler,
	EconomyAccountingInstructionImportHandler,
} from './Economy/Instruction'

import {
	EconomyAccountsHandler,
	EconomyAccountHandler,
	EconomyAccountEditHandler,
	EconomyAccountAddHandler,
} from './Economy/Account'

import {
	EconomyValuationSheetHandler,
} from './Economy/ValuationSheet'

import {
	EconomyResultReportHandler,
} from './Economy/ResultReport'

import {
	EconomyCostCentersHandler,
	EconomyCostCenterHandler,
} from './Economy/CostCenter'

import { InvoiceListHandler, InvoiceHandler, InvoiceAddHandler } from './Economy/Invoice'

import { GroupsHandler, GroupHandler, GroupAddHandler } from './groups'
import { LabAccessHandler } from './labaccess'
import { MembersHandler, MemberHandler, MemberAddHandler } from './member'
import { MailHandler } from './mail'
import { Nav, SideNav, SideNav2 } from './nav'
import { SettingsHandler } from './settings'
import { StatisticsHandler } from './statistics'
import { DashboardHandler, ExportHandler, Loading } from './temp'

var nav = new Backbone.Model({
	brand: "Makerspace Internal v2",
	navItems:
	[
		{
			text: "Medlemmar",
			target: "/member/list",
			children:
			[
				{
					text: "Visa medlemmar",
					target: "/member/list",
					children: [
						{
							text: "Medlem",
							target: "/member/:id",
						},
					],
				},
				{
					text: "Skapa medlem",
					target: "/member/add",
				},
			],
		},
		{
			text: "Grupper",
			target: "/group/list",
			children:
			[
				{
					text: "Visa grupper",
					target: "/group/list",
				},
				{
					text: "Skapa grupp",
					target: "/group/add",
				},
			],
		},
		{
			text: "Prenumerationer",
			target: "/labaccess",
			children:
			[
				{
					text: "Prenumerationer",
					target: "/labaccess",
				},
				{
					text: "Typer",
					target: "/labaccess/types",
				},
			],
		},
		{
			text: "Ekonomi",
			target: "/economy",
			children:
			[
				{
					text: "Översikt",
					target: "/economy",
				},
				{
					text: "Huvudbok",
					target: "/economy/masterledger",
				},
				{
					text: "Verifikationer",
					target: "/economy/instruction",
					children:
					[
						{
							text: "",
							target: "/economy/instruction/:id",
						},
					],
				},
				{
					text: "Fakturor",
					target: "/economy/invoice",
					children:
					[
						{
							text: "",
							target: "/economy/invoice",
						},
					],
				},
				{
					text: "Balansrapport",
					target: "/economy/valuationsheet",
				},
				{
					text: "Resultatrapport",
					target: "/economy/resultreport",
				},
				{
					text: "Kostnadsställen",
					target: "/economy/costcenter",
				},
				{
					type: "separator",
					target: "",
				},
				{
					type: "heading",
					text: "Inställningar",
					target: "",
				},
				{
					text: "Konton",
					target: "/economy/accounts",
				},
				{
					text: "Räkneskapsår",
					target: "/economy/accountingperiods",
				},
			],
		},
		{
			text: "Mail",
			target: "/mail",
		},
		{
			text: "Inställningar",
			target: "/settings",
		},
		{
			text: "Export",
			target: "/members/export",
		},
		{
			text: "Statistik",
			target: "/statistics",
		},
	]
});


var App = React.createClass({
	render()
	{
		var key = this.props.location.pathname;

		return (
			<div>
				<Nav model={nav} />
				<SideNav model={nav} />

				<div className="uk-container uk-container-center uk-margin-top">
					<div className="uk-grid">

						<div className="uk-width-medium-1-4">
							<SideNav2 model={nav} />
						</div>

						<div className="uk-width-medium-3-4">
							{this.props.children}
						</div>
					</div>
				</div>
			</div>
		);
	}
});

var NoMatch = React.createClass({
	render: function()
	{
		return (<h1>404</h1>);
	}
});

React.render((
	<Router history={history}>
		<Route path="/" component={App}>
			<IndexRoute component={DashboardHandler} />
			<Route path="/member/add"  component={MemberAddHandler} />
			<Route path="/member/list" component={MembersHandler} />
			<Route path="/member/:id"  component={MemberHandler} />
			<Route path="labaccess"    component={LabAccessHandler} />
			<Route path="economy">
				<IndexRoute component={EconomyOverviewHandler} />
				<Route path="masterledger"     component={MasterLedgerHandler} />

				<Route path="invoice"          component={InvoiceListHandler} />
				<Route path="invoice/add"      component={InvoiceAddHandler} />
				<Route path="invoice/:id"      component={InvoiceHandler} />

				<Route path="accounts"         component={EconomyAccountsHandler} />
				<Route path="account/add"      component={EconomyAccountAddHandler} />
				<Route path="account/:id"      component={EconomyAccountHandler} />
				<Route path="account/:id/edit" component={EconomyAccountEditHandler} />

				<Route path="instruction"      component={EconomyAccountingInstructionsHandler} />
				<Route path="instruction/:id"  component={EconomyAccountingInstructionHandler} />
				<Route path="instruction/:id/import" component={EconomyAccountingInstructionImportHandler} />

				<Route path="valuationsheet"   component={EconomyValuationSheetHandler} />
				<Route path="resultreport"     component={EconomyResultReportHandler} />

				<Route path="costcenter"       component={EconomyCostCentersHandler} />
				<Route path="costcenter/:id"   component={EconomyCostCenterHandler} />
			</Route>
			<Route path="members/export" component={ExportHandler} />
			<Route path="statistics"     component={StatisticsHandler} />
			<Route path="mail"           component={MailHandler} />
			<Route path="group/list"     component={GroupsHandler} />
			<Route path="group/add"      component={GroupAddHandler} />
			<Route path="group/:id"      component={GroupHandler} />
			<Route path="settings"       component={SettingsHandler} />
			<Route path="*"              component={NoMatch}/>
		</Route>
	</Router>
), document.body);