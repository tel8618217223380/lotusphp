<?php
/**
 * 用于加工DB句柄query方法返回的数组
 * 开发者在一次会话中可配置多个Factory
 */
class LtDbSqlMapResultFactory {
	/**
	 * 工厂入口，sql map client调用的方法
	 * 在这个方法中调用开发者自定义的
	 * LtAbstractSqlMapObjectFactory.process()方法
	 * 可配置多个process方法
	 */
	public function run() {
	}
}

